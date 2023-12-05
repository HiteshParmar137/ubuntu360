<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ExportProjectReportJob;

class UsersController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }
    public function index(Request $request)
    {
        try {
            $usersList = User::Query();
            (isset($request->search) && $request->search != '')
                ? $usersList = $usersList->where(function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->search . '%');
                })
                : '';

            (isset($request->user_type) && $request->user_type != '')
                ? $usersList = $usersList->where('user_type', $request->user_type)
                : '';

            $sort =  (isset($request->sort) && isset($request->sort) != '')
                ? $request->sort : 'id';

            $direction =  (isset($request->direction) && $request->direction != '')
                ? $request->direction : 'DESC';

            $usersList = $usersList->orderBy($sort, $direction);
            $usersList = $usersList->paginate(env('PAGINATION_COUNT'));

            $response = [];
            if ($usersList->count() > 0) {
                $page =  (!empty($request->page)) ? $request->page : 1;
                foreach ($usersList as $k => $user) {
                    $usersArr = [];
                    $usersArr['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                    $usersArr['incryptedId'] = encryptId($user->id);
                    $usersArr['id'] = $user->id;
                    $usersArr['name'] = $user->name;
                    $usersArr['email'] = $user->email;
                    $usersArr['signup_completed'] = $user->is_signup_completed == 1 ? 'Completed' : 'Not Completed';
                    $usersArr['status'] = $user->status == 1 ? 'Active' : 'Inactive';
                    $response[] = $usersArr;
                }
            }
            $paginateData = $usersList->toArray();
            $pagination =  pagination($paginateData);

            // if no data found
            if ($usersList->count() == 0) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.common_message.error_message')
                ], 200);
            }

            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_users_management.fetch_success'),
                'data' => compact('response', 'pagination')
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

    public function getUserDetails(Request $request, $id)
    {
        try {
            //$id = decryptId($id);
            $user = User::where('id', $id)->with('hasUserDetails', 'hasSdgsDetails', 'hasSdgsDetails.hasSdgs')->first();
            $response = User::getUserDetails($user);

            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.front_user.user_details_fetch_success'),
                'data' =>  $response
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

    /* Listing of user reports */
    public function reportUserList(Request $request)
    {
        try {
            $lists = User::query();
            if (isset($request->email) && $request->email != '') {
                $lists = $lists->where('email', 'LIKE', '%' . $request->email . '%');
            }
            if (isset($request->position) && $request->position != '') {
                $lists = $lists->whereHas('hasUserDetails', function ($query) use ($request) {
                    $query->where('position', 'LIKE', '%' . $request->position . '%');
                });
            }
            if (isset($request->sponserType) && $request->sponserType != '') {
                $lists = $lists->where('user_type', $request->sponserType);
            }
            $lists = $lists->with(
                'hasUserDetails',
                'hasSdgsDetails.hasSdgs',
                'hasInterestDetails.hasCategory',
                'hasProjects',
                'hasDonations',
                'hasProjectFollows'
            );
            $lists = $lists->orderBy('id', 'desc')->paginate(env('PAGINATION_COUNT'));

            $postData = $request->all();
            $page = $postData['page'];
            $userArray = [];
            $response = [];
            $dataArr = [];
            if ($lists->count() > 0) {
                foreach ($lists as $k => $list) {
                    $userArray['sl_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                    $userArray['id'] = $list->id;
                    $userArray['name'] = $list->name;
                    $userArray['email'] = !empty($list->email) ? $list->email : "";

                    $userArray['dob'] = !empty($list->hasUserDetails)
                        ? dateformat($list->hasUserDetails->dob)  : "";

                    $userArray['location'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->location : "";
                    $userArray['contact_number'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->contact_number : "";

                    if ($list->user_type == '1') {
                        $userArray['sponsor_type'] = 'Individual';
                    } elseif ($list->user_type == '2') {
                        $userArray['sponsor_type'] = 'Corporate';
                    }

                    $userArray['corporation_name'] = !empty($list->hasUserDetails) ?
                        $list->hasUserDetails->corporation_name : "";

                    $userArray['industry'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->industry : "";

                    $userArray['city'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->city : "";

                    $userArray['country'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->country : "";

                    $userArray['contact_name'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->contact_name : "";

                    $userArray['position'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->position : "";

                    $sdgsName = '';
                    foreach ($list->hasSdgsDetails as $sdgs) {
                        $sdgsName .= $sdgs->hasSdgs->name . ',';
                    }
                    $userArray['sdg_names'] = rtrim($sdgsName, ',');


                    $userArray['twitter_id'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->twitter : '';

                    if ((!empty($list->hasUserDetails) && $list->hasUserDetails->twitter != null)
                        && $list->hasUserDetails->twitter == 1
                    ) {
                        $userArray['twitter'] = config('constants.common_not_at_all');
                    } elseif ((!empty($list->hasUserDetails) && $list->hasUserDetails->twitter != null)
                        && $list->hasUserDetails->twitter == '2'
                    ) {
                        $userArray['twitter'] = config('constants.common_rarely');
                    } elseif ((!empty($list->hasUserDetails) && $list->hasUserDetails->twitter != null)
                        && $list->hasUserDetails->twitter == '3'
                    ) {
                        $userArray['twitter'] = config('constants.common_regular');
                    } else {
                        $userArray['twitter'] = '';
                    }

                    $userArray['facebook_id'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->facebook : '';

                    if ((!empty($list->hasUserDetails) && $list->hasUserDetails->facebook != null)
                        && $list->hasUserDetails->facebook == 1
                    ) {
                        $userArray['facebook'] = config('constants.common_not_at_all');
                    } elseif ((!empty($list->hasUserDetails) && $list->hasUserDetails->facebook != null)
                        && $list->hasUserDetails->facebook == '2'
                    ) {
                        $userArray['facebook'] = config('constants.common_rarely');
                    } elseif ((!empty($list->hasUserDetails) && $list->hasUserDetails->facebook != null)
                        && $list->hasUserDetails->facebook == '3'
                    ) {
                        $userArray['facebook'] = config('constants.common_regular');
                    } else {
                        $userArray['facebook'] = '';
                    }

                    $userArray['linkedin_id'] = !empty($list->hasUserDetails)
                        ? $list->hasUserDetails->linkedin : '';

                    if ((!empty($list->hasUserDetails) && $list->hasUserDetails->linkedin != null)
                        && $list->hasUserDetails->linkedin == 1
                    ) {
                        $userArray['linkedin'] = config('constants.common_not_at_all');
                    } elseif ((!empty($list->hasUserDetails) && $list->hasUserDetails->linkedin != null)
                        && $list->hasUserDetails->linkedin == '2'
                    ) {
                        $userArray['linkedin'] = config('constants.common_rarely');
                    } elseif ((!empty($list->hasUserDetails) && $list->hasUserDetails->linkedin != null)
                        && $list->hasUserDetails->linkedin == '3'
                    ) {
                        $userArray['linkedin'] = config('constants.common_regular');
                    } else {
                        $userArray['linkedin'] = '';
                    }

                    $hasInterestName = '';
                    foreach ($list->hasInterestDetails as $interests) {
                        $hasInterestName .= $interests->hasCategory->name . ',';
                    }
                    $userArray['category'] = rtrim($hasInterestName, ',');

                    $userArray['total_project'] = !empty($list->hasProjects)
                        ? $list->hasProjects->count() : "";

                    $userArray['donation_amount'] = (!empty($list->hasDonations)
                        && $list->hasDonations->count() > 0)
                        ?  number_format($list->hasDonations->sum('donation_amount'), 2, '.', ',')
                        : '0';

                    $userArray['tips_amount'] = (!empty($list->hasDonations)
                        && $list->hasDonations->count() > 0)
                        ?  number_format($list->hasDonations->sum('tips_amount'), 2, '.', ',')
                        : '0';

                    $userArray['total_project_follow'] = !empty($list->hasProjectFollows)
                        ? $list->hasProjectFollows->count() : "";

                    $userArray['signup_completed'] = $list->is_signup_completed == 1 ? 'Completed' : 'Not Completed';

                    $response[] = $userArray;

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
                'message' => config('message.user_listing_fetched_success'),
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

    /* This function is used for export the user list */
    public function exportUserReportList(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:user',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return response()->json(apiResponse(200, false, $message, null), 200);
        }

        try {
            $data = [
                'type' => config('constants.user_type'),
                'file_name' => 'user_report_' . date("Y_m_d") . '.xlsx',
            ];
            /* Calling the Job */
            dispatch(new ExportProjectReportJob($data, $input));
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.user_email_notification'),
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
}
