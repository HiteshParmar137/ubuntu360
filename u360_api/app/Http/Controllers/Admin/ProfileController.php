<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    public function adminProfile(Request $request)
    {
        try {
            $user = jwtAuthUser();
            $userImage = checkFileExitOrNot($user->image, public_path(config('constants.admin_profile_img_path')));
            $userImage = $userImage === true ?
                asset(config('constants.admin_profile_img_path') . '/' . $user->image)
                : asset(config('constants.no_user_image_path'));
            $response = [];
            $response['userDetails']['id'] = $user->id ?? '';
            $response['userDetails']['name'] = $user->name ?? '';
            $response['userDetails']['email'] = $user->email ?? '';
            $response['userDetails']['status'] = $user->status ?? '';
            $response['userDetails']['profile_image'] =  $userImage;
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_profile.fetched_success'), 'data' => $response
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

    public function profileChange(Request $request)
    {
        try {
            $user = jwtAuthUser();
            $validatArray['name'] = 'required';
            $validatArray['email'] = 'required|email|unique:admin_users,email,' . $user->id;
            $messages = [];
            if ($request->file('image')) {
                $validatArray['image'] = 'mimes:png,jpeg,gif|max:2048';
                $messages = [
                    'image.max' => 'The image may not be greater than 2mb.',
                ];
            }
            $validator = Validator::make($request->all(), $validatArray, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }
            $postData = $request->all();
            $name = $postData['name'] ?? '';
            $email = $postData['email'] ?? '';
            $oldImage = $user->image ?? '';
            $response = [];
            $userArr = [
                'name' =>  $name,
                'email' =>  $email,
            ];
            if ($request->file('image')) {
                $newImgName = fileUploadPublic($request->file('image'), config('constants.admin_profile_img_path'));
                if (isset($oldImage) && !empty($oldImage)) {
                    fileDeletePublic($oldImage, config('constants.admin_profile_img_path'));
                }
                $oldImage = $newImgName;
                $userArr['image'] = $newImgName;
            }
            $oldImage = asset(config('constants.admin_profile_img_path') . '/' . $oldImage);
            $user->update($userArr);
            $response['profileDetails']['name'] = $name;
            $response['profileDetails']['email'] = $email;
            $response['profileDetails']['profile_image'] = $oldImage;
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_profile.update_success'), 'data' => $response
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
