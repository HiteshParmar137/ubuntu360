<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ExportProjectReportJob;
use App\Jobs\ThankYouRecurringJob;
use App\Models\Country;
use App\Models\Project;
use App\Models\ProjectFollow;
use App\Models\ProjectRejectReason;
use App\Models\Sdg;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\TransactionService;

use function Ramsey\Uuid\v1;

class ProjectController extends Controller
{
    protected $currentPath;
    protected $projectObj;
    protected $transactionServiceObj;
    public function __construct(TransactionService $transaction)
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
        $this->projectObj = new ProjectService();
        $this->transactionServiceObj = $transaction;
    }

    public function getProjectsList(Request $request)
    {
        try {
            $projectArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'srchByTitle' => isset($request->title) && $request->title != '' ? $request->title : '',
                'srchByCategory' => isset($request->category) && $request->category != '' ? $request->category : '',
                'srchByUser' => isset($request->user) && $request->user != '' ? $request->user : '',
                'srchByStatus' => isset($request->status) && $request->status != '' ? $request->status : '',
            ];
            $project = $this->projectObj->index($projectArr);
            if ($project['success'] === false) {
                $message = config('message.front_user.projects_not_available');
                return response()->json(apiResponse(200, false, $message, null), 200);
            }
            $message = config('message.front_user.projects_fetch_success');
            return response()->json(apiResponse(200, true, $message, $project), 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    public function deleteProject(Request $request, $id)
    {
        try {
            $id = decryptId($id);
            $poject = Project::find($id);
            if (empty($poject)) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => config('message.admin_project.not_found'),
                    'data' => null
                ], 200);
            }
            $poject->delete();
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.admin_project.delete_success'),
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    public function getProjectUsers(Request $request)
    {
        try {
            $response = [];
            $pojectUsers = Project::query();
            $pojectUsers = $pojectUsers->select('user_id');
            $pojectUsers = $pojectUsers->with(['hasUser' => function ($query) {
                $query->select('id', 'name');
            }]);
            $pojectUsers = $pojectUsers->groupBy('user_id');
            $pojectUsers = $pojectUsers->get();
            if (isset($pojectUsers) && $pojectUsers->count()) {
                foreach ($pojectUsers as $projectUser) {
                    $projectUsersArr = [];
                    $projectUsersArr['id'] = $projectUser->hasUser->id ?? 0;
                    $projectUsersArr['userName'] = $projectUser->hasUser->name ?? '';
                    $response[] = $projectUsersArr;
                }
                return response()->json([
                    'status_code' => 200,
                    'success' => true,
                    'message' => config('message.admin_project.users_fetch_success'),
                    'data' => $response
                ], 200);
            }
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    public function getProjectDetails(Request $request, $id)
    {
        try {
            $projectArr = [
                'id' => $id
            ];
            $response = $this->projectObj->details($projectArr);
            if ($response['success'] === false) {
                return response([
                    'status_code' => 200,
                    'success' => false,
                    'message' => $response['message'],
                    'data' => null
                ], 200);
            }
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => $response['message'],
                'data' => $response['response']
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    public function getProjectDocuments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'type' => 'required|in:image,video,document',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200,
                    'success' => true,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }
            $paramsArr = [
                'id' => $request->id,
                'type' => $request->type,
            ];
            $projectDocuments = $this->projectObj->getProjectDocs($paramsArr);
            if ($projectDocuments['success'] === false) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => $projectDocuments['message'], 'data' => null
                ], 200);
            }
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => $projectDocuments['message'],
                'data' => $projectDocuments['response']
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    public function save(Request $request, $stepType, $stepNo)
    {
        try {
            if ($stepNo && $stepNo > 0) {
                $validationArray = [
                    'project_type' => 'required|numeric',
                    'title' => 'required',
                    'description' => 'required',
                    'city' => 'required',
                    'country' => 'required|numeric',
                    'sdg_id' => 'required|numeric',
                    'category_ids' => 'required'
                ];
                $inputArray = [
                    'id', 'project_type', 'title', 'description', 'city', 'country', 'sdg_id', 'category_ids'
                ];
            }
            $validator = Validator::make($request->all(), $validationArray);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200,
                    'success' => true,
                    'message' => $validator->errors(),
                    'data' => null
                ], 200);
            }
            $postData = $request->only($inputArray);
            $postData['stepType'] = $stepType;
            $projectData = $this->projectObj->saveProjectDetails($postData);
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => $projectData['message'],
                'data' => ['project_id' => $projectData['response']]
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    public function saveProjectDocument(Request $request)
    {
        try {
            $validationArray = [
                'project_id' => 'required',
                'type' => 'required|in:image,video,document',
            ];
            $type = $request->type;
            if ($type && $type == 'image') {
                $validationArray['document'] = 'required|mimes:png,jpg,jpeg|max:10240';
            } elseif ($type && $type == 'video') {
                $validationArray['document'] = 'required|mimes:mp4,flv,wmv|max:10240';
            } else {
                $validationArray['document'] = 'required|mimes:pdf,doc,xls,xlsx,csv,ppt|max:10240';
            }
            $validator = Validator::make($request->all(), $validationArray);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200,
                    'success' => true,
                    'message' => $validator->errors(),
                    'data' => null
                ], 200);
            }

            $dataArr = $request->only('project_id', 'type');
            $postData = [
                'dataArr' => $dataArr,
                'document' => $request->file('document'),
            ];
            $projDocData = $this->projectObj->saveProjectDocuments($postData);
            if ($projDocData['success'] === false) {
                return response([
                    'status_code' => 200, 'success' => false,
                    'message' => $projDocData['message'], 'data' => null
                ], 200);
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => $projDocData['message'], 'data' => $projDocData['response']
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500, 'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    public function deleteProjectDocument(Request $request, $id)
    {
        try {
            $paramsArr = [
                'id' => $id
            ];
            $deletedDocObj = $this->projectObj->deleteProjectDocument($paramsArr);
            if ($deletedDocObj['success'] === false) {
                return response()->json(
                    [
                        'status_code' => 200,
                        'success' => false,
                        'message' => $deletedDocObj['message'],
                        'data' => null
                    ],
                    200
                );
            }

            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => $deletedDocObj['message'], 'data' => null
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500, 'success' => false,
                'message' => config('message.common_message.exception_error'), 'data' => null
            ], 500);
        }
    }

    public function getFollowedProjectsList(Request $request)
    {
        try {
            $projectArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'srchByTitle' => isset($request->title) && $request->title != '' ? $request->title : '',
                'srchByCategory' => isset($request->category) && $request->category != '' ? $request->category : '',
                'srchByStatus' => isset($request->status) && $request->status != '' ? $request->status : '',
            ];
            $followedProjects = ProjectFollow::select('project_id')->where('user_id', $request->user)->get();
            $projectIds = [];
            if (!empty($followedProjects)) {
                foreach ($followedProjects as $value) {
                    array_push($projectIds, $value->project_id);
                }
            }
            $projectArr['projectIds'] = $projectIds;
            $project = $this->projectObj->index($projectArr);
            if ($project['success'] === false) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => config('message.front_user.projects_not_available'),
                    'data' => null
                ], 200);
            }
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.front_user.projects_fetch_success'),
                'data' =>  $project
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }
    public function getProjectFollowersList(Request $request)
    {
        try {
            $projectArr = [
                'page' => !empty($request->page) ? $request->page : 1,
            ];
            $followedProjects = ProjectFollow::select('project_id')->where('user_id', $request->user)->get();
            $projectIds = [];
            if (!empty($followedProjects)) {
                foreach ($followedProjects as $value) {
                    array_push($projectIds, $value->project_id);
                }
            }
            $projectArr['project_ids'] = [decryptId($request->id)];
            $project = $this->projectObj->getProjectFollowersList($projectArr);
            if ($project['success'] === false) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => config('message.front_user.projects_not_available'),
                    'data' => null
                ], 200);
            }
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.front_user.projects_fetch_success'),
                'data' =>  $project
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }
    public function getProjectDonationList(Request $request)
    {
        try {
            $paramsArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'search_by' => 'my_project_donation',
            ];
            $paramsArr['project_ids'][0] = decryptId($request->id);
            $transactions = $this->transactionServiceObj->getDonationDetails($paramsArr);
            if ($transactions['success'] === false) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => config('message.common_message.error_message'),
                    'data' => null
                ], 200);
            }

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.transactions.fetch_success'),
                'data' => $transactions
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }
    public function getProjectVolunteerList(Request $request)
    {
        try {
            $paramsArr = [
                'page' => !empty($request->page) ? $request->page : 1,
            ];
            $paramsArr['project_id'] = decryptId($request->id);
            $volunteer = $this->projectObj->getVolunteerList($paramsArr);
            if ($volunteer['success'] === false) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => config('message.common_message.error_message'),
                    'data' => null
                ], 200);
            }

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.transactions.fetch_success'),
                'data' => $volunteer
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }
    public function approveProject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }
            $id = decryptId($request->project_id);
            $project = Project::where('id', $id)->first();
            if (empty($project)) {
                $message = config('message.admin_project.not_found');
                $response = apiResponse(200, false, $message, null);
            } else {
                $user = User::where('id', $project->user_id)->first();
                $project->status = "Approved";
                $project->save();
                $mailBody = getTemplateInfo('project_approved', 'email', [
                    '##NAME##' => !empty($user->name) ? $user->name : '',
                    '##PROJECT_TITLE##' => $project->title,
                ]);
                $mailData = [
                    'message' => $mailBody['message'] ?? '',
                    'subject' => $mailBody['subject'] ?? '',
                ];
                if (!empty($user->email)) {
                    sendEmail($user->email ?? '', $mailData);
                }

                $message = config('message.admin_project.project_approved');
                $response = apiResponse(200, true, $message, null);
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }
    /* This function id used for
    *  reject Project
    */
    public function rejectProject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required',
                'comment' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }
            $id = decryptId($request->project_id);
            $project = Project::where('id', $id)->first();
            if (empty($project)) {
                $message = config('message.admin_project.not_found');
                $response = apiResponse(200, false, $message, null);
            } else {
                $user = User::where('id', $project->user_id)->first();
                $project->status = "Rejected";
                $project->save();

                // insert reason into child table
                ProjectRejectReason::create([
                    'project_id' => $project->id,
                    'user_id' => $project->user_id,
                    'comment' => $request->comment,
                ]);

                $mailBody = getTemplateInfo('project_rejected', 'email', [
                    '##NAME##' => !empty($user->name) ? $user->name : '',
                    '##PROJECT_TITLE##' => $project->title,
                    '##COMMENT##' => $request->comment,
                ]);
                $mailData = [
                    'message' => $mailBody['message'] ?? '',
                    'subject' => $mailBody['subject'] ?? '',
                ];
                if (!empty($user->email)) {
                    sendEmail($user->email, $mailData);
                }

                $message = config('message.admin_project.project_rejected');
                $response = apiResponse(200, true, $message, null);
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    /* this function is used for listing the project report  */
    public function reportProjectList(Request $request)
    {
        try {
            $lists = Project::query();
            if (isset($request->email) && $request->email != '') {
                $lists = $lists->whereHas('hasUser', function ($query) use ($request) {
                    $query->where('email', 'LIKE', '%' . $request->email . '%');
                });
            }
            if (isset($request->type) && $request->type != '') {
                $lists = $lists->where('project_type', $request->type);
            }
            if (isset($request->donation_type) && $request->donation_type != '') {
                $lists = $lists->where('project_donation_type', 'LIKE', '%' . $request->donation_type . '%');
            }
            if (isset($request->category) && $request->category != '') {
                $lists = $lists->where('category_id', $request->category);
            }
            if (isset($request->status) && $request->status != '') {
                $lists = $lists->where('status', $request->status);
            }
            if (isset($request->created_at) && $request->created_at != '') {
                $lists = $lists->where('created_at', 'LIKE', '%' . $request->created_at . '%');
            }
            $lists = $lists->with(
                'hasUser',
                'hasUser.hasUserDetails',
                'hasDonations',
                'hasProjectCategory',
                'hasVolunteer'
            );

            $lists = $lists->orderBy('created_at', 'desc')->paginate(env('PAGINATION_COUNT'));

            $postData = $request->all();
            $page = $postData['page'];
            $projectArray = [];
            $response = [];
            $dataArr = [];
            if ($lists->count() > 0) {
                foreach ($lists as $k => $list) {
                    $projectArray['sl_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                    $projectArray['project_id'] = $list['id'];
                    $projectArray['user_name'] = !empty($list->hasUser) ? $list->hasUser->name : "";
                    $projectArray['user_email'] = !empty($list->hasUser) ? $list->hasUser->email : "";
                    $projectArray['user_contact'] = !empty($list->hasUser->hasUserDetails)
                        ? $list->hasUser->hasUserDetails->contact_number : "";
                    $projectArray['title'] = $list->title;
                    $projectArray['description'] = $list->description;


                    switch ($list->project_type) {
                        case '1':
                            $projectArray['project_type'] = 'New';
                            break;
                        case '2':
                            $projectArray['project_type'] = 'Existing';
                            break;
                        case '3':
                            $projectArray['project_type'] = 'Emergency Relief';
                            break;
                        default:
                            $projectArray['project_type'] = '';
                    }

                    $projectDonationTypeIds = explode(",", $list->project_donation_type);
                    $projectTypeValue = "";
                    foreach ($projectDonationTypeIds as $projectType) {
                        ($projectType == 1) ? $projectTypeValue .= 'Donation,' : '';
                        ($projectType == 2) ? $projectTypeValue .= 'Volunteer,' : '';
                    }
                    $projectArray['project_donation_type'] = rtrim($projectTypeValue, ',');

                    $projectArray['category_name'] = $list->hasProjectCategory->name;
                    $projectArray['amount'] = number_format($list->amount, 2, '.', ',');

                    $projectArray['donation_amount'] =  ($list->hasDonations->count() > 0)
                        ? number_format($list->hasDonations->sum('donation_amount'), 2, '.', ',')
                        : 0;

                    $projectArray['volunteer'] =  $list->volunteer ?? 0;

                    $projectArray['joined_volunteer'] =  ($list->hasVolunteer->count() > 0)
                        ? $list->hasVolunteer->count('id')
                        : 0;

                    $projectArray['tips_amount'] =  ($list->hasDonations->count() > 0)
                        ? number_format($list->hasDonations->sum('tips_amount'), 2, '.', ',')
                        : 0;

                    $projectArray['city'] = !empty($list->city) ? $list->city : "";
                    if (!empty($list->country)) {
                        $name = Country::select('name')->where('id', $list->country)->first();
                        $projectArray['countury'] = $name->name;
                    } else {
                        $projectArray['countury'] = "";
                    }

                    if (!empty($list->sdg_ids)) {
                        $explodSdgId = explode(",", $list->sdg_ids);

                        $sdgNames = Sdg::select('name')->whereIn('id', $explodSdgId)->get();

                        $sdgsName = '';
                        foreach ($sdgNames as $sdgs) {
                            $sdgsName .= $sdgs->name . ',';
                        }
                        $projectArray['sdg_names'] = rtrim($sdgsName, ',');
                    } else {
                        $projectArray['sdg_names'] = "";
                    }
                    $projectArray['sdg_ids'] = !empty($list->sdg_ids) ? $list->sdg_ids : "";
                    $projectArray['status'] = !empty($list->status) ? $list->status : "";
                    $projectArray['created_at'] = !empty($list->created_at)
                        ? dateformat($list->created_at)  : "";

                    $response[] = $projectArray;
                }

                $paginateData = $lists->toArray();
                $pagination =  pagination($paginateData);
                $dataArr = [
                    'response' => $response,
                    'pagination' => $pagination,
                ];
                if ($lists->count() == 0) {
                    $dataArr['success'] = false;
                    $dataArr['message'] = 'No data found';
                }
            }

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.admin_project.project_report_fetched_success'),
                'data' => $dataArr
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    /* this function is used for export the project report  */
    public function exportProjectList(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:project',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return response()->json(apiResponse(200, false, $message, null), 200);
        }

        try {
            $data = [
                'type' => config('constants.project_type'),
                'file_name' => 'project_report_' . date("Y_m_d") . '.xlsx',
            ];
            /* Calling the Job */
            dispatch(new ExportProjectReportJob($data, $input));
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.project_email_notification'),
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    /* this function is used for send the email (thankyou,recurring)  */
    public function checkReachGoal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return response()->json(apiResponse(200, false, $message, null), 200);
        }

        try {
            dispatch(new ThankYouRecurringJob($request->project_id));
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }
}
