<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Jobs\ExportReportJob;

class EsgReportsController extends Controller
{
    public $currentPath;

    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    /* This function id used for send the
    *  emport excel file into email
    */
    public function exportEsgReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:esg',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return response()->json(apiResponse(200, false, $message, null), 200);
        }

        try {
            $data = [
                'type' => config('constants.esg_type'),
                'file_name' => 'esg_report_' . date("Y_m_d") . '.xlsx',
            ];

            /* Calling the Job */
            dispatch(new ExportReportJob($data));
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.esg_email_notification'),
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

    /* This function is used for
    * export subscription emails
    */
    public function exportSubscriptionReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:subscription',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return response()->json(apiResponse(200, false, $message, null), 200);
        }

        try {
            $data = [
                'type' => config('constants.subscription_type'),
                'file_name' => 'subscription_' . date("Y_m_d") . '.xlsx',
            ];

            /* Calling the Job */
            dispatch(new ExportReportJob($data));
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.subscription_email_notification'),
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
