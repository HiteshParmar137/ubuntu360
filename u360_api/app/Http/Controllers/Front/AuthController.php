<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Models\DeviceToken;
use App\Services\GeneralService;
use Illuminate\Support\Facades\Password;
use Spatie\FlareClient\Api;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $currentPath;
    protected $generalServicesObj;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
        $this->generalServicesObj = new GeneralService();
    }
    /**
     * User Login Api Function
     */
    public function userLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password'  =>  "required_if:social_id,=,null",
                'social_id' =>  "required_if:password,=,null",
                'device_type' => 'required|numeric',
                'device_token' => 'required_if:device_type,1|required_if:device_type,2',
            ]);
            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }
            $response = [];
            $user = User::where(['email' => $request->email])->first();
            try {
                if (!empty($user) && !empty($request->password) && Hash::check($request->password, $user->password)) {
                    if (!$token = JWTAuth::fromUser($user)) {
                        $message = config('message.auth_message.given_credentials_are_invalid');
                        return response()->json(apiResponse(200, false, $message, null), 200);
                    }
                } elseif (!empty($user) && !empty($request->social_id)) {
                    $user = $user->where('social_id', $request->social_id)->first();
                    if (!isset($user) && empty($user)) {
                        $user = User::where(['email' => $request->email])->first();
                        $user->social_id = $request->social_id;
                        $user->save();
                        $token = JWTAuth::fromUser($user);
                    } else {
                        $token = JWTAuth::fromUser($user);
                    }
                } else {
                    if (!empty($request->email) && !empty($request->social_id)) {
                        $userArray = [
                            'name' => $request->name,
                            'email' => $request->email,
                            'password' => '',
                            'user_type' => '2',
                            'status' => '1',
                            'social_id' => $request->social_id,
                            'is_email_verified' => '1',
                            'email_verify_token' => ''
                        ];
                        $user = User::create($userArray);
                        $token = JWTAuth::fromUser($user);
                    } else {
                        $message = config('message.auth_message.given_credentials_are_invalid');
                        return response()->json(apiResponse(200, false, $message, null), 200);
                    }
                }
            } catch (JWTException $e) {
                Log::info([
                    'Route' => $this->currentPath,
                    'Request' => $request->all(),
                    'Error' => $e->getMessage(),
                    'Line' => $e->getLine()
                ]);
                $message = config('message.auth_message.token_could_not_create');
                return response()->json(apiResponse(200, false, $message, null), 200);
            }
            if ($user['status'] != 1) {
                $message = config('message.auth_message.admin_inactive');
                return response()->json(apiResponse(200, false, $message, null), 200);
            }
            if ($user['is_email_verified'] != 1) {
                return response()->json(
                    [
                        'status_code' => 200,
                        'success' => false,
                        'message' => config('message.front_user.user_email_not_verify'),
                        'data' => null
                    ],
                    200
                );
            }
            if (isset($request->device_type) && isset($request->device_token)) {
                $deviceTokenArr = [
                    'user_id' => $user['id'] ?? '',
                    'device_type' => $request->device_type ?? 0,
                    'device_token' => $request->device_token ?? '',
                ];
                DeviceToken::create($deviceTokenArr);
                $response['device_token'] = $request->device_token ?? '';
            }

            $response['device_type'] = $request->device_type ?? 0;


            $userImage = checkFileExitOrNot($user['image'], public_path(config('constants.user_profile_img_path')));
            $userImage = $userImage === true ?
                asset(config('constants.user_profile_img_path') . '/' . $user['image'])
                : asset(config('constants.no_user_image_path'));

            $response['token'] = $token;
            $response['id'] = $user['id'];
            $response['name'] = $user['name'];
            $response['is_signup_completed'] = $user['is_signup_completed'];
            $response['user_type'] =  (isset($user['user_type'])) ? $user['user_type'] : '';
            $response['status'] =  ($user['status'] == 1) ? 'Online' : 'Offline';
            $response['userImage'] = $userImage;
            $user->api_token = $token;
            if ($user->is_signup_completed == 1) {
                $user->last_logged_in = date('Y-m-d H:i:s');
            } else {
                $user->last_logged_in = null;
            }
            $user->save();

            return response()->json(
                [
                    'status_code' => 200,
                    'success' => true,
                    'message' => config('message.auth_message.user_logged_in_successfully'),
                    'data' => $response
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
            $message = config('message.common_message.exception_error');
            return response()->json(apiResponse(200, false, $message, null), 500);
        }
    }

    public function logOut(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_type' => 'required|numeric',
                'device_token' => 'required_if:device_type,1|required_if:device_type,2',
            ]);
            if ($validator->fails()) {
                $returnRresponse =  response()->json([
                    'status_code' => 200, 'success' => true,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            } else {
                $deviceTokens = DeviceToken::where('user_id', jwtAuthUser()->id)
                    ->where('device_type', $request->device_type)
                    ->where('device_token', $request->device_token)->get();
                if ($deviceTokens && !empty($deviceTokens)) {
                    foreach ($deviceTokens as $deviceToken) {
                        $deviceToken->delete();
                    }
                }
                Auth::guard('api')->logout();
                $returnRresponse = response()->json([
                    'status_code' => 200, 'success' => true,
                    'message' => 'User successfully signed out', 'data' => null
                ], 200);
            }
            return $returnRresponse;
        } catch (TokenExpiredException $e) {
            $returnRresponse = response()->json([
                'status_code' => 401, 'success' => false,
                'message' => config('message.auth_message.token_expired'), 'data' => null
            ], 401);
        } catch (TokenInvalidException $e) {
            $returnRresponse = response()->json([
                'status_code' => 401, 'success' => false,
                'message' => config('message.auth_message.invalid_token'), 'data' => null
            ], 401);
        } catch (JWTException $e) {
            $returnRresponse = response()->json([
                'status_code' => 404, 'success' => false,
                'message' => config('message.auth_message.token_not_found'), 'data' => null
            ], 404);
        }
        return $returnRresponse;
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker(): \Illuminate\Contracts\Auth\PasswordBroker
    {
        return Password::broker('users');
    }

    public function userSendResetLinkResponse(Request $request)
    {
        try {
            $input = $request->only('email');
            $validator = Validator::make($input, [
                'email' => "required|email"
            ]);
            if ($validator->fails()) {
                $returnRresponse = response([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors()->all(), 'data' => null
                ], 200);
            } else {
                $user = User::where('email', $request->email)->first();
                if ($user == null) {
                    $returnRresponse = response([
                        'status_code' => 200, 'success' => false,
                        'message' => config('message.front_user.auth_user_not_found'), 'data' => null
                    ], 200);
                } else {
                    $verifyToken = Str::random(64);
                    $message=config('message.front_user.auth_mail_sent_succes');
                    $returnRresponse = $this->generalServicesObj->sendForgotPasswordMail($user, $verifyToken, $message);
                }
            }

            return $returnRresponse;
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

    protected function userSendResetResponse(Request $request)
    {
        try {
            $input = $request->only('token', 'password', 'confirm_password');
            $validator = Validator::make($input, [
                'token' => 'required',
                'password' => 'required|string|min:8',
                'confirm_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                $returnRresponse = response([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors()->all(), 'data' => null
                ], 200);
            } else {
                $user = User::where('reset_password_token', $request->token)->first();
                if ($user == null) {
                    $returnRresponse = response([
                        'status_code' => 200, 'success' => false,
                        'message' => config('message.front_user.auth_user_not_found'), 'data' => null
                    ], 200);
                } else {
                    $user->forceFill(['password' => Hash::make($request->password)])->save();
                    $returnRresponse = response()->json([
                        'status_code' => 200, 'success' => true,
                        'message' => config('message.front_user.auth_password_reset_success'), 'data' => null
                    ], 200);
                }
            }
            return $returnRresponse;
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

    public function userPasswordChange(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|string|min:8',
                'confirm_password' => 'required|same:new_password',
            ]);

            if ($validator->fails()) {
                $returnRresponse = response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            } else {
                if (empty(auth()->user())) {
                    $returnRresponse = response([
                        'status_code' => 200, 'success' => false,
                        'message' => config('message.front_user.auth_user_not_found'), 'data' => null
                    ], 200);
                } else {
                    $adminUser = User::where('id', auth()->user()->id)->first();
                    if (!(Hash::check(request('current_password'), $adminUser->password))) {
                        $returnRresponse = response([
                            'status_code' => 200, 'success' => false,
                            'message' => config('message.front_user.auth_wrong_password_error'), 'data' => null
                        ], 200);
                    } else {
                        if (strcmp($request->current_password, $request->new_password) == 0) {
                            $returnRresponse =  response([
                                'status_code' => 200, 'success' => false,
                                'message' => config('message.front_user.auth_password_compare_error'),
                                'data' => null
                            ], 200);
                        } else {
                            //update admin_user password
                            $adminUser->password = Hash::make($request->new_password);
                            $adminUser->save();

                            $returnRresponse = response()->json([
                                'status_code' => 200, 'success' => true,
                                'message' => config('message.front_user.auth_change_pwd_success'), 'data' => null
                            ], 200);
                        }
                    }
                }
            }
            return $returnRresponse;
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
