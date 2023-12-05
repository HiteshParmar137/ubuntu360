<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    public function index(Request $request)
    {
        try {
            $response = [];
            $response['job_posting']['title'] = ["Technology", "Information & Technology"];
            $response['job_posting']['value'] = [192, 22];
            $response['active_job_seeker']['total_candidate'] = 12;
            $response['active_job_seeker']['total_active_job_seeker'] = 7;

            $response['active_job_posting']['total_jobs'] = 232;
            $response['active_job_posting']['active_job'] = 39;
            $response['total_candidate'] = 12;
            $response['total_employers'] = 83;
            $response['total_hires'] = "6";

            // Success
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => '', 'data' => compact('response')
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
                'message' => config('message.common_message.exception_error')
            ], 500);
        }
    }
}
