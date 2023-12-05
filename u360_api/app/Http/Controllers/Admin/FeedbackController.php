<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }
    public function index(Request $request)
    {
        try {
            $feedbackList = Feedback::Query();
            $feedbackList = $feedbackList->with('hasUser');
            $sort =  (isset($request->sort) && isset($request->sort) != '') ? $request->sort : 'id';

            $direction = (isset($request->direction) && $request->direction != '')
                ? $request->direction : 'DESC';

            $feedbackList = $feedbackList->orderBy($sort, $direction);
            $feedbackList = $feedbackList->paginate(env('PAGINATION_COUNT'));

            $response = [];
            if ($feedbackList->count() > 0) {
                $page =  (!empty($request->page)) ? $request->page : 1;
                foreach ($feedbackList as $k => $feedback) {
                    $feedbacksArr = [];
                    $feedbacksArr['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                    $feedbacksArr['id'] = encryptId($feedback->id);
                    $feedbacksArr['userName'] = $feedback->hasUser->name ?? '';
                    $feedbacksArr['comment'] = $feedback->comment ?? '';
                    $feedbacksArr['rating'] = $feedback->rating ?? 0;
                    $feedbacksArr['created_at'] = dateformat($feedback->created_at) ?? '';
                    $response[] = $feedbacksArr;
                }
            }
            $paginateData = $feedbackList->toArray();
            $pagination =  pagination($paginateData);

            // if no data found
            if ($feedbackList->count() == 0) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.common_message.error_message')
                ], 200);
            }

            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_feedback.fetch_success'),
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
                'message' => config('message.common_message.exception_error'), 'data' => null
            ], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $id = decryptId($id) ?? 0;
            $feedbackExist = Feedback::find($id);
            if (empty($feedbackExist)) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.admin_feedback.not_found'), 'data' => null
                ], 200);
            }
            $feedbackExist->delete();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_feedback.delete_success'), 'data' => null
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
}
