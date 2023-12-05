<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ProjectCommunityComment;
use App\Services\ProjectCommunityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use App\Models\ProjectCommunityLike;
use App\Services\ProjectService;

class ProjectCommunityController extends Controller
{
    protected $currentPath;
    protected $projectCommunityService;
    protected $projectObj;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
        $this->projectCommunityService = new ProjectCommunityService();
        $this->projectObj = new ProjectService();
    }

    //create function for add community post data
    public function addCommunity(Request $request)
    {
        try {
            $validationArray = [
                'project_id' => 'required',
                'comment' => 'required',
            ];
            if (isset($request->document) && $request->document != '') {
                $validationArray['document'] = 'mimes:png,jpg,jpeg,mp4,flv,wmv|max:10240';
            }
            $validator = Validator::make($request->all(), $validationArray);
            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }
            $postData = $request->all();
            $id = decryptId($request->project_id);
            $postData['project_id'] = $id;
            $response = $this->projectCommunityService->addPost($postData);
            return response()->json(
                apiResponse(200, $response['status'], $response['message'], $response['post_data']),
                200
            );
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
    // api for all project comunity list
    public function getAllCommunityList(Request $request)
    {
        try {
            $postData = [
                'page' => !empty($request->page) ? $request->page : 1,
            ];
            $postData['project_id'] = '';

            $response = $this->projectCommunityService->getCommunityList($postData);
            return response()->json(
                apiResponse(200, $response['status'], $response['message'], $response),
                200
            );
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
    // api for project details page comunity list
    public function getCommunityList(Request $request)
    {
        try {
            $postData = [
                'page' => !empty($request->page) ? $request->page : 1,
            ];
            $postData['project_id'] = decryptId($request->project_id);
            $response = $this->projectCommunityService->getCommunityList($postData);
            return response()->json(
                apiResponse(200, $response['status'], $response['message'], $response),
                200
            );
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
    // api for project details page comunity like and unlike
    public function addLike(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required',
                'project_community_id' => 'required',
                'type' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }
            $user = jwtAuthUser();
            $postData = $request->all();
            $communityId = decryptId($postData['project_community_id']);
            $existRecords = ProjectCommunityLike::where('user_id', $user->id)
                ->where('project_id', decryptId($postData['project_id']))
                ->where('project_community_id', decryptId($postData['project_community_id']))->first();
            if ($postData['type'] && $postData['type'] == 'like') {
                if (!empty($existRecords)) {
                    $message = 'community_already_like';
                } else {
                    $like = new ProjectCommunityLike;
                    $like->user_id = $user->id;
                    $like->project_id = decryptId($postData['project_id']);
                    $like->project_community_id = decryptId($postData['project_community_id']);
                    $like->save();
                    $message = 'like_success';
                }
            } else {
                if ($existRecords && !empty($existRecords)) {
                    ProjectCommunityLike::where('user_id', $user->id)
                        ->where('project_community_id', $communityId)->delete();
                }
                $message = 'unlike_success';
            }
            $totalCount = ProjectCommunityLike::where('project_community_id', $communityId)
                ->count();
            $data['total_like'] = numberToShort($totalCount);
            return response()->json(apiResponse(200, true, config('message.front_user.' . $message), $data), 200);
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

    public function addComment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required',
                'project_community_id' => 'required',
                'comment' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }
            $postData = $request->only('project_id', 'comment', 'project_community_id');
            $communityId = decryptId($postData['project_community_id']);
            $user = jwtAuthUser();
            $postData['user_id'] = $user->id ?? 0;
            $comment = new ProjectCommunityComment;
            $comment->user_id = $user->id;
            $comment->project_id = decryptId($postData['project_id']);
            $comment->project_community_id = decryptId($postData['project_community_id']);
            $comment->comment = $postData['comment'];
            $comment->save();
            $message = config('message.front_user.comment_add_success');
            $commentData['name'] = $user->name;
            $commentData['comment'] = $comment->comment;
            $commentData['profile_image'] = (!empty($user->image))
                ? asset(config('constants.user_profile_img_path') . '/' . $user->image ?? '')
                : asset(config('constants.no_user_image_path'));
            $totalCount = ProjectCommunityComment::where('project_community_id', $communityId)
                ->count();
            $commentData['total_comment'] = numberToShort($totalCount);
            return response()->json(apiResponse(200, true, $message, $commentData), 200);
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

    public function getUserProjectCommunityList(Request $request)
    {
        try {
            $postData = [
                'page' => !empty($request->page) ? $request->page : 1,
            ];
            $user = jwtAuthUser();
            $postData['project_id_arr'] = $this->projectObj->getProjectIdFromUserId($user->id);

            $response = $this->projectCommunityService->getCommunityList($postData);
            return response()->json(
                apiResponse(200, $response['status'], $response['message'], $response),
                200
            );
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            $message = config('message.common_message.exception_error');
            $message = $e->getMessage();
            return response()->json(apiResponse(500, false, $message, null), 500);
        }
    }
}
