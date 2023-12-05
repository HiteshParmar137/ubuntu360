<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ESGReport;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EsgReportsController extends Controller
{
    public $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    /* this funcation is used to save the subscription email*/
    public function saveEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:subscriptions',
            ]);
            if ($validator->fails()) {
                $message = $validator->errors();
                return response()->json(apiResponse(200, false, $message, null), 200);
            }
            $dataArr = [
                'email' => $request->email ?? '',
            ];
            Subscription::create($dataArr);
            $message = config('message.front_user.esg_report_email_add_success');
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

    /* this funcation is used for listing of esg report */
    public function getEsgReport(Request $request)
    {
        try {
            $esgReport = ESGReport::Query();

            (isset($request->email) && $request->email != '')
                ? $esgReport = $esgReport->where('email', 'LIKE', '%' . $request->email . '%') : '';

            $sort = (isset($request->sort) && isset($request->sort) != '') ? $request->sort : 'id';

            $direction = (isset($request->direction) && isset($request->direction) != '')
                ? $request->direction : 'DESC';

            $esgReport = $esgReport->orderBy($sort, $direction);
            $esgReport = $esgReport->paginate(env('PAGINATION_COUNT'));

            $response = [];
            if ($esgReport->count() > 0) {
                $page = (!empty($request->page)) ? $request->page : 1;
                foreach ($esgReport as $k => $user) {
                    $esgArr = [];
                    $esgArr['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                    $esgArr['id'] = encryptId($user->id);
                    $esgArr['email'] = $user->email;
                    $response[] = $esgArr;
                }
            }
            $paginateData = $esgReport->toArray();
            $pagination =  pagination($paginateData);

            // if no data found
            if ($esgReport->count() == 0) {
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
                'message' => config('message.admin_esg_report.fetch_success'),
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
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    /* this funcation is used for delete the esg report */
    public function delete(Request $request, $id)
    {
        try {
            $id = decryptId($id) ?? 0;
            $esgExist = ESGReport::find($id);
            if (empty($esgExist)) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => config('message.admin_esg_report.not_found'),
                    'data' => null
                ], 200);
            }
            $esgExist->delete();
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.admin_esg_report.delete_success'),
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

    public function saveESGEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:esg_reports',
            ]);
            if ($validator->fails()) {
                $message = $validator->errors();
                return response()->json(apiResponse(200, false, $message, null), 200);
            }
            $dataArr = [
                'email' => $request->email ?? '',
            ];
            ESGReport::create($dataArr);
            $message = config('message.front_user.esg_report_email_add_success');
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

    /* this function is used for list of subscriptions */
    public function getSubscription(Request $request)
    {
        try {
            $subscription = Subscription::Query();

            (isset($request->email) && $request->email != '') ?
                $subscription = $subscription->where('email', 'LIKE', '%' . $request->email . '%') : '';

            $sort = (isset($request->sort) && isset($request->sort) != '') ? $request->sort : 'id';
            $direction = (isset($request->direction) && $request->direction != '')
                ? $request->direction : 'DESC';

            $subscription = $subscription->orderBy($sort, $direction);
            $subscription = $subscription->paginate(env('PAGINATION_COUNT'));

            $response = [];
            if ($subscription->count() > 0) {
                $page =  (!empty($request->page)) ? $request->page : 1;
                foreach ($subscription as $k => $user) {
                    $esgArr = [];
                    $esgArr['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                    $esgArr['id'] = encryptId($user->id);
                    $esgArr['email'] = $user->email;
                    $response[] = $esgArr;
                }
            }
            $paginateData = $subscription->toArray();
            $pagination =  pagination($paginateData);

            // if no data found
            if ($subscription->count() == 0) {
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
                'message' => config('message.admin_subscription.fetch_success'),
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
                'status_code' => 500,
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /* this function is used for list of delete subscriptions email*/
    public function deleteSubscriptionEmail(Request $request, $id)
    {
        try {
            $id = decryptId($id) ?? 0;
            $subExist = Subscription::find($id);
            if (empty($subExist)) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => config('message.admin_subscription.not_found'),
                    'data' => null
                ], 200);
            }
            $subExist->delete();
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.admin_subscription.delete_success'),
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
