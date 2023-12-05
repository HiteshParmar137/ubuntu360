<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    protected $currentPath;
    private $projectObj;
    public function __construct(ProjectService $projectObj)
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
        $this->projectObj = $projectObj;
    }

    public function index(Request $request)
    {
        try {
            $response = [];
            $data = User::select('id')
                ->with(['hasDonations.hasTransactions' => function ($donation) {
                    $donation->select(
                        'transactions.*',
                        DB::raw('sum(transactions.amount) as transactionSum'),
                        DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),
                        DB::raw('MONTHNAME(created_at) month')
                    )->where('type', 'Donation')->groupBy('month');
                }])->where('user_type', '1')->get();
            $newArr = [];
            if (isset($data) && !empty($data)) {
                foreach ($data as $user) {
                    if ($user->hasDonations->count() > 0) {
                        foreach ($user->hasDonations as $donation) {
                            if ($donation->hasTransactions->count() > 0) {
                                foreach ($donation->hasTransactions as $transactions) {
                                    $newArr['month'][] = $transactions->month;
                                    $newArr['value'][] = $transactions->transactionSum;
                                }
                            }
                        }
                    }
                }
            }
            dd($newArr);
            return response()->json(
                [
                    'status_code' => 200,
                    'success' => true,
                    'message' => config('message.front_user.user_details_fetch_success'),
                    'data' =>  $response
                ],
                200
            );
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json(
                [
                    'status_code' => 500,
                    'success' => false,
                    'message' => config('message.common_message.exception_error'),
                    'data' => null
                ],
                500
            );
        }
    }

    public function userProjectDashboard(Request $request)
    {
        try {
            $response = [];
            $paramsArr['page'] = 1;
            $paramsArr['user'] = 0;

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
                'data' => null
            ], 500);
        }
    }

    public function sponserChart(Request $request)
    {
        try {
            $response = [];
            $filter = [];
            $filter['user'] = 0;
            if (isset($request->searchType) && $request->searchType != '') {
                $filter['searchType'] = $request->searchType;
            } else {
                $filter['searchType'] = 'month';
            }
            $response = $this->projectObj->sponserChart($filter);

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.common_message.success_message'),
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

    public function dashboardMap(Request $request)
    {
        try {
            $filter = [];
            $filter['user'] = 0;
            if (isset($request->searchType) && $request->searchType != '') {
                $filter['searchType'] = $request->searchType;
            } else {
                $filter['searchType'] = 'project';
            }
            if (isset($request->valueType) && $request->valueType != '') {
                $filter['valueType'] = $request->valueType;
            } else {
                $filter['valueType'] = 'normal';
            }
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
                // 'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 200);
        }
    }

    public function countsForDashboardCards(Request $request)
    {
        try {
            $filter = [];
            $filter['user'] = 0;
            $response = $this->projectObj->countsForDashboardCards($filter);
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.common_message.success_message'),
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

    public function userChart(Request $request)
    {
        try {
            $response = [];
            $filter = [];
            $filter['user'] = 0;
            if (isset($request->searchType) && $request->searchType != '') {
                $filter['searchType'] = $request->searchType;
            } else {
                $filter['searchType'] = 'week';
            }
            if (isset($request->status) && $request->status != '') {
                $filter['status'] = $request->status;
            } else {
                $filter['status'] = 'all';
            }
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
}
