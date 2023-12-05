<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectDonation;
use App\Models\ProjectFollow;
use App\Models\ProjectReview;
use App\Models\ProjectVolunteer;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    protected $currentPath;
    protected $projectObj;
    protected $transactionService;
    public function __construct(TransactionService $transactionService)
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
        $this->projectObj = new ProjectService();
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $response = [];
        $projectArr = [
            'status' => ['Pending', 'Completed']
        ];
        $response['myProjects'] = $this->projectObj->userProjectsCount($projectArr) ?? 0;
        $response['myDonations'] = $this->projectObj->userTotalDonations() ?? 0;
        $response['reviewRatingProjectList'] = $this->projectObj->recentReviewProjectsList() ?? [];
        return response()->json([
            'status_code' => 200, 'success' => true,
            'message' => config('message.front_user.user_dashboard_fetch_success'),
            'data' => $response
        ], 200);
    }

    public function userProjectDashboard(Request $request)
    {
        try {
            $response = [];
            $userId = jwtAuthUser()->id;
            $paramsArr['page'] = 1;
            $paramsArr['user'] = 1;
            // $paramsArr['project_ids'] = [];
            // $response['my_project_donations'] = [];
            // $paramsArr['project_followed_ids'] = [];
            // if (!empty($userId)) {
            //     $project = Project::select('id')->where('user_id', $userId)->get()->toArray();
            //     if (!empty($project)) {
            //         foreach ($project as $key => $value) {
            //             $paramsArr['project_ids'][$key] = $value['id'];
            //         }
            //     }
            //     $donation = ProjectDonation::query();
            //     $donation = $donation->whereIn('project_id', $paramsArr['project_ids']);
            //     $donation = $donation->orderBy('created_at', 'DESC')->limit(3)->get()->toArray();
            //     $response['my_project_donations'] = $donation;
            //     if (!empty($response['my_project_donations'])) {
            //         foreach ($response['my_project_donations'] as $key => $val) {
            //             $user = User::find($val['user_id']);
            //             $response['my_project_donations'][$key]['user_name'] = $user->name;
            //             if (isset($user->image) && $user->image != ''){
            //                 $response['my_project_donations'][$key]['profile_image'] =
            //                 asset(config('constants.user_profile_img_path') . '/' . $user->image);
            //             } else {
            //                 $response['my_project_donations'][$key]['profile_image'] = '';
            //             }

            //             $project = Project::find($val['project_id']);
            //             $response['my_project_donations'][$key]['project_title'] = $project->title;
            //         }
            //     }

            //     $review = ProjectReview::query();
            //     $review = $review->whereIn('project_id', $paramsArr['project_ids']);
            //     $review = $review->orderBy('created_at', 'DESC')->limit(4)->get()->toArray();
            //     $response['my_project_ratings'] = $review;
            //     if (!empty($response['my_project_ratings'])) {
            //         foreach ($response['my_project_ratings'] as $key => $val) {
            //             $response['my_project_ratings'][$key]['starsHTML'] =
            //             $this->projectObj->getRatingStartsHTML($val['rating']);

            //             $project = Project::find($val['project_id']);
            //             $response['my_project_ratings'][$key]['project_title'] = $project->title;

            //             $user = User::find($val['user_id']);
            //             $response['my_project_ratings'][$key]['user_name'] = $user->name;
            //             if (isset($user->image) && $user->image != ''){
            //                 $response['my_project_ratings'][$key]['profile_image'] =
            //                 asset(config('constants.user_profile_img_path') . '/' . $user->image);
            //             } else {
            //                 $response['my_project_ratings'][$key]['profile_image'] = '';
            //             }
            //         }
            //     }

            //     $favouriteProjectIds = ProjectFollow::select('project_id')
            //         ->where('user_id', $userId)
            //         ->orderBy('created_at', 'DESC')
            //         ->get()
            //         ->toArray();
            //     if (!empty($favouriteProjectIds)) {
            //         foreach ($favouriteProjectIds as $key => $value) {
            //             $paramsArr['project_followed_ids'][$key] = $value['project_id'];
            //         }
            //     }
            //     $fav_pro = Project::query();
            //     $fav_pro = $fav_pro->whereIn('id', $paramsArr['project_followed_ids']);
            //     $fav_pro = $fav_pro->withCount([
            //         'hasTransactions AS totalAmount' => function ($transaction) {
            //             $transaction->where('type', 'Donation')->select(DB::raw("SUM(amount)"));
            //         }
            //     ])
            //     ->get();
            //     $fav_pro->toArray();
            //     $response['favourite_projects'] = $fav_pro;
            //     if (!empty($fav_pro->toArray())) {
            //         foreach ($fav_pro->toArray() as $key => $value) {
            //             $percentageAmount = 0;
            //             if ($value['amount'] && $value['amount'] != null && $value['amount'] > 0) {
            //                 $percentageAmount = $value['totalAmount'] * 100 / $value['amount'];
            //             }
            //             $response['favourite_projects'][$key]['totalDonationPercentage'] =
            //                 $percentageAmount;
            //             $response['favourite_projects'][$key]['leftAmount'] =
            //                 $response['favourite_projects'][$key]['amount'] -
            //                 $response['favourite_projects'][$key]['totalAmount'];
            //         }
            //     }

            //     $recentUsers = User::where('is_signup_completed', '1')
            //         ->with('hasUserDetails')
            //         ->orderBy('created_at', 'DESC')
            //         ->limit(4)
            //         ->get()
            //         ->toArray();
            //     $users = [];
            //         foreach ($recentUsers as $key => $value) {
            //             $users[$key]['name'] = $value['name'];
            //             $users[$key]['location'] = $value['name'];
            //             $users[$key]['user_type'] = $value['has_user_details']['location'] ?? '';
            //             $users[$key]['mobile'] = $value['has_user_details']['contact_number'] ?? '';
            //             if (!empty($value['image'])) {
            //                 $users[$key]['image'] =
            //                 asset(config('constants.user_profile_img_path') . '/' . $value['image']);;
            //             } else {
            //                 $users[$key]['image'] = '';
            //             }
            //             $users[$key]['email'] = $value['email'];
            //         }
            //     $response['recent_users'] = $users;
            // }
            $response = $this->projectObj->userProjectDashboard($paramsArr);
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.front_user.user_dashboard_fetch_success'),
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
                'message' => $e->getMessage(),
                // 'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    public function countsForDashboardCards(Request $request)
    {
        try {
            $filter = [];
            $filter['user'] = 1;
            $response = $this->projectObj->countsForDashboardCards($filter);

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.front_user.user_dashboard_fetch_success'),
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
                'message' => $e->getMessage(),
                // 'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    public function sponserChart(Request $request)
    {
        try {
            $response = [];
            $filter = [];
            $filter['user'] = 1;
            $filter['searchType'] = (isset($request->searchType) && $request->searchType != '')
                ? $request->searchType : 'month';

            $response = $this->projectObj->sponserChart($filter);
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.front_user.user_dashboard_fetch_success'),
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

    public function userChart(Request $request)
    {
        try {
            $response = [];
            $filter = [];
            $filter['user'] = 1;
            $filter['searchType'] = (isset($request->searchType) && $request->searchType != '')
                ? $request->searchType : 'week';
            $filter['status'] = (isset($request->status) && $request->status != '')
                ? $request->status : 'all';
            $response = $this->projectObj->userChart($filter);
            if ($response['success']) {
                return response()->json([
                    'status_code' => 200,
                    'success' => true,
                    'message' => config('message.front_user.user_dashboard_fetch_success'),
                    'data' => $response
                ], 200);
            } else {
                return response()->json([
                    'status_code' => 500,
                    'success' => false,
                    'message' => config('message.front_user.user_dashboard_fetch_failure'),
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
                // 'message' => $e->getMessage(),
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 200);
        }
    }

    public function dashboardMap(Request $request)
    {
        try {
            $filter = [];
            $filter['user'] = 1;
            $filter['searchType'] = (isset($request->searchType) && $request->searchType != '')
                ? $request->searchType : 'project';

            $filter['valueType'] = (isset($request->valueType) && $request->valueType != '')
                ? $request->valueType : 'normal';
            $response = $this->projectObj->dashboardMap($filter);
            if ($response['success']) {
                return response()->json([
                    'status_code' => 200,
                    'success' => true,
                    'message' => config('message.front_user.user_dashboard_fetch_success'),
                    'data' => $response
                ], 200);
            } else {
                return response()->json([
                    'status_code' => 500,
                    'success' => false,
                    'message' => config('message.front_user.user_dashboard_fetch_failure'),
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
                'message' => $e->getMessage(),
                'data' => null
            ], 200);
        }
    }
}
