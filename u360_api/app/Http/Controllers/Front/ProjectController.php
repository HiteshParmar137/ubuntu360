<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\ProjectFollow;
use App\Models\ProjectCommunity;
use App\Models\ProjectDonation;
use App\Models\ProjectReview;
use App\Models\ProjectVolunteer;
use App\Services\ProjectService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    protected $currentPath;
    protected $projectObj;
    protected $transactionService;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
        $this->projectObj = new ProjectService();
        $this->transactionService = new TransactionService();
    }

    public function createComment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required',
                'comment' => 'required',
                'rating' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => true,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }
            $user = jwtAuthUser();
            $postData = $request->only('project_id', 'comment', 'rating');
            $postData['user_id'] = $user->id ?? 0;
            ProjectComment::updateOrCreate([
                'user_id' => $postData['user_id'],
                'project_id' => $postData['project_id'],
            ], $postData);
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.front_user.comment_add_success'), 'data' => null
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

    public function createFollow(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200,
                    'success' => true,
                    'message' => $validator->errors(),
                    'data' => null
                ], 200);
            }
            $user = jwtAuthUser();
            $postData = $request->only('project_id', 'type');
            $postData['project_id'] = decryptId($postData['project_id']);
            $postData['user_id'] = $user->id ?? 0;
            if ($postData['type'] && $postData['type'] == 'follow') {
                unset($postData['type']);
                ProjectFollow::updateOrCreate([
                    'user_id' => $postData['user_id'],
                    'project_id' => $postData['project_id'],
                ], $postData);
                $message = 'follow_success';
            } else {
                $existRecords = ProjectFollow::where('user_id', $postData['user_id'])
                    ->where('project_id', $postData['project_id'])
                    ->get();
                if ($existRecords && !empty($existRecords)) {
                    foreach ($existRecords as $follow) {
                        $follow->delete();
                    }
                }
                $message = 'unfollow_success';
            }
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.front_user.' . $message),
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

    public function save(Request $request, $stepType, $stepNo)
    {
        try {
            if ($stepNo && $stepNo > 0) {
                $validationArray = [
                    'project_type' => 'required|numeric',
                    'title' => 'required',
                    // 'amount' => 'required',
                    'project_donation_type' => 'required',
                    'description' => 'required',
                    'city' => 'required',
                    'country' => 'required|numeric',
                    'sdg_ids' => 'required',
                    'category_id' => 'required'
                ];
                $inputArray = [
                    'id', 'project_type', 'title', 'amount', 'project_donation_type',
                    'description', 'project_url','city', 'country', 'sdg_ids', 'category_id', 'volunteer'
                ];
                $requestData = $request->all();
                if (isset($requestData['amount'])) {
                    $validationArray['amount'] = 'required';
                }
                if (isset($requestData['volunteer'])) {
                    $validationArray['volunteer'] = 'required';
                }
            }
            $validator = Validator::make($request->all(), $validationArray);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }
            $postData = $request->only($inputArray);
            $user = jwtAuthUser();
            $userId = $user->id ?? 0;
            $postData['user_id'] = $userId;
            $postData['stepType'] = $stepType;
            $projectData = $this->projectObj->saveProjectDetails($postData);
            return response()->json([
                'status_code' => 200, 'success' => true,
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
                'status_code' => 500, 'success' => false,
                'message' => config('message.common_message.exception_error'), 'data' => null
            ], 500);
        }
    }

    public function saveProjectDocument(Request $request)
    {
        try {
            $validationArray = [
                'project_id' => 'required',
                'type' => 'required|in:image,video,document,cover_image',
            ];
            $type = $request->type;
            if ($type && $type == 'cover_image') {
                $validationArray['document'] = 'required|mimes:png,jpg,jpeg|max:5120'; //5mb
                $messages = [
                    'document.max' => 'The cover image may not be greater than 5mb.',
                ];
            } elseif ($type && $type == 'image') {
                $validationArray['document'] = 'required|mimes:png,jpg,jpeg|max:5120'; //5mb
                $messages = [
                    'document.max' => 'The image may not be greater than 5mb.',
                ];
            } elseif ($type && $type == 'video') {

                $validationArray['document'] = 'required|mimes:mp4,flv,wmv|max:10240';
                //10mb
                $messages = [
                    'document.max' => 'The video may not be greater than 10mb.',
                ];
            } else {
                $validationArray['document'] = 'required|mimes:pdf,doc,xls,xlsx,csv,ppt|max:5120'; //5mb
                $messages = [
                    'document.max' => 'The document may not be greater than 5mb.',
                ];
            }
            $validator = Validator::make($request->all(), $validationArray, $messages);
            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }

            $dataArr = $request->only('project_id', 'type');
            $user = jwtAuthUser();
            $userId = $user->id;
            $postData = [
                'dataArr' => $dataArr,
                'userId' => $userId,
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
                'message' => config('message.common_message.exception_error'), 'data' => null
            ], 500);
        }
    }

    public function userProjectDetails(Request $request, $id)
    {
        try {
            $userId = jwtAuthUser()->id;
            $projectArr = [
                'id' => $id,
                'user_id' => $userId,
            ];
            $response = $this->projectObj->details($projectArr);
            if ($response['success'] === false) {
                return response([
                    'status_code' => 200, 'success' => false,
                    'message' => $response['message'], 'data' => null
                ], 200);
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => $response['message'], 'data' => $response['response']
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

    public function getUserProjectsList(Request $request)
    {
        try {
            $projectArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'user' => 1,
                'srchByTitle' => !empty($request->title) ? $request->title : '',
                'srchByStatus' => !empty($request->status) ? $request->status : '',
            ];
            $projectArr['status'] = ['Draft','Pending', 'Approved', 'Rejected'];
            $project = $this->projectObj->index($projectArr);
            if ($project['success'] === false) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.front_user.projects_not_available'), 'data' => null
                ], 200);
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.front_user.projects_fetch_success'), 'data' =>  $project
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

    public function deleteProject(Request $request, $id)
    {
        try {
            $id = decryptId($id);
            $user = jwtAuthUser();
            $poject = Project::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($poject)) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' =>  config('message.front_user.project_not_found'), 'data' => null
                ], 200);
            }
            $poject->delete();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.front_user.project_delete_success'), 'data' => null
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
    public function deleteProjectCoverImage(Request $request, $id)
    {
        try {
            $paramsArr = [
                'id' => $id
            ];
            $deletedDocObj = $this->projectObj->deleteProjectCoverImage($paramsArr);
            if ($deletedDocObj['success'] === false) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $deletedDocObj['message'], 'data' => null
                ], 200);
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
    public function deleteProjectDocument(Request $request, $id)
    {
        try {
            $paramsArr = [
                'id' => $id
            ];
            $deletedDocObj = $this->projectObj->deleteProjectDocument($paramsArr);
            if ($deletedDocObj['success'] === false) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $deletedDocObj['message'], 'data' => null
                ], 200);
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

    public function getProjectDocuments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'type' => 'required|in:image,video,document',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => true,
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
                    'status_code' => 200, 'success' => false,
                    'message' => $projectDocuments['message'], 'data' => null
                ], 200);
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => $projectDocuments['message'], 'data' => $projectDocuments['response']
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

    public function getProjectsList(Request $request)
    {
        try {
            $projectArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'status' => ['Approved'],
                'srchByTitle' => isset($request->title) && $request->title != '' ? $request->title : '',
            ];
            if (!empty($request->category)) {
                $projectArr['srchByCategory'] = $request->category;
            }
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

    public function projectDetails(Request $request, $id)
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
                'data' => $response
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

    public function closeProject(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = jwtAuthUser();
            $id = decryptId($id);
            $projectObj = Project::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($projectObj)) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' =>  config('message.front_user.project_not_found'), 'data' => null
                ], 200);
            }
            $projectDonation=ProjectDonation::where('project_id',$id)->where('is_recurring_stop','0')->get();
            if(!empty($projectDonation)){
                foreach($projectDonation as $donation){
                    $params['donation_id']=$donation->id;
                    $this->transactionService->stopRecurringDonation($params);
                }
            }
            $projectObj->update(
                ['status' => 'Completed by Owner']
            );
            DB::commit();
            $mailBody = getTemplateInfo('project_closed', 'email', [
                '##NAME##' => $user->name ?? '',
            ]);
            $mailData = [
                'message' => $mailBody['message'] ?? '',
                'subject' => $mailBody['subject'] ?? '',
            ];
            
            sendEmail(config('constants.admin_mail') ?? '', $mailData);

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.front_user.project_closed_success'),
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            $message = config('message.common_message.exception_error');
            return response()->json(apiResponse(500, false, $message, null), 500);
        }
    }

    // add project volunteer
    public function addVolunteer(Request $request)
    {
        try {
            $user = jwtAuthUser();
            $validator = Validator::make($request->all(), [
                'project_id' => 'required',
                'email' => 'required',
                'volunteer_type' => 'required|numeric',
                'comment' => 'required',
            ]);
            $isemailExit = ProjectVolunteer::where('project_id', decryptId($request->project_id))
                ->where('email', $request->email)->first();
            if (!empty($isemailExit)) {
                return response()->json(apiResponse(200, false, 'The email has already been taken.', null), 200);
            }
            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }
            $postData = $request->only('volunteer_type', 'email', 'comment', 'project_id');
            $volunteerArr = [
                'user_id' => $user->id,
                'project_id' => decryptId($postData['project_id']),
                'volunteer_type' => $postData['volunteer_type'],
                'email' => $postData['email'],
                'comment' => $postData['comment']
            ];
            $volunteerData = ProjectVolunteer::create($volunteerArr);
            $projectcommunity = new ProjectCommunity;
            $projectcommunity->user_id = $volunteerData->user_id;
            $projectcommunity->project_id = $volunteerData->project_id;
            $projectcommunity->volunteer_id = $volunteerData->id;
            $projectcommunity->type = '2';
            $projectcommunity->save();
            $message = config('message.front_user.volunteer_add_success');
            return response()->json(apiResponse(200, true, $message, null), 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            $message = config('message.common_message.exception_error');
            return response()->json(apiResponse(500, false, $message, null), 500);
        }
    }

    public function getFollowedProjectsList(Request $request)
    {
        try {
            $projectArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'status' => ['Pending', 'Completed by Owner', 'Approved', 'Goal Reached'],
                'srchByTitle' => isset($request->title) && $request->title != '' ? $request->title : '',
            ];
            $user = jwtAuthUser();
            $followedProjects = ProjectFollow::select('project_id')->where('user_id', $user->id)->get();
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
    //add review and rating api
    public function saveReview(Request $request)
    {
        try {
            $validationArray = [
                'projectId' => 'required',
                'rating' => 'required',
                'comment' => 'required',
            ];
            $validator = Validator::make($request->all(), $validationArray);
            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }
            $user = jwtAuthUser();
            $userId = $user->id ?? 0;
            $projectId = decryptId($request->projectId);
            $projectReview = ProjectReview::where(['user_id' => $userId, 'project_id' => $projectId])->first();
            if (!empty($projectReview)) {
                $message = config('message.front_user.project_already_added_review');
                $response = apiResponse(200, false, $message, null);
            } else {
                $projectReviewObj = new ProjectReview();
                $projectReviewObj->user_id = $userId;
                $projectReviewObj->project_id = $projectId;
                $projectReviewObj->rating = $request->rating;
                $projectReviewObj->comment = $request->comment;
                $projectReviewObj->save();
                $message = config('message.front_user.project_review_add_success');
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

    // Front user completed project list
    public function getCompletedProjectsList(Request $request)
    {
        try {
            $projectArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'status' => ['Goal Reached', config('constants.completed_by_owner')],
                'srchByTitle' => isset($request->title) && $request->title != '' ? $request->title : '',
                'user' => 1
            ];
            if (!empty($request->category)) {
                $projectArr['srchByCategory'] = $request->category;
            }
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

    public function getAllReviews(Request $request)
    {
        try {
            $projectArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'project_id' => isset($request->project_id) && $request->project_id != '' ? $request->project_id : '',
            ];
            $reviews = $this->projectObj->getAllReviews($projectArr);
            if ($reviews['success'] === false) {
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
                'data' =>  $reviews
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
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
