<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminUser;
use App\Models\AdminModule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\GeneralService;

class AuthController extends Controller
{
	protected $currentPath;
	protected $generalServicesObj;
	public function __construct()
	{
		$this->currentPath = Route::getFacadeRoot()->current()->uri();
		$this->generalServicesObj = new GeneralService();
	}

	public function adminLogin(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				'email' => 'required|email',
				'password' => 'required|string|min:6',
			]);
			if ($validator->fails()) {
				return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
			}
			$admin = AdminUser::where(['email' => $request->email])->with('usersGroup')->first();
			try {
				if (!empty($admin) && Hash::check($request->password, $admin->password)) {
					if (!$token = JWTAuth::fromUser($admin)) {
						$message = config('message.auth_message.given_credentials_are_invalid');
						return response()->json(apiResponse(200, false, $message, null), 200);
					}
				} else {
					$message = config('message.auth_message.given_credentials_are_invalid');
					return response()->json(apiResponse(200, false, $message, null), 200);
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
			if ($admin['status'] != 1) {
				$message = config('message.auth_message.admin_inactive');
				return response()->json(apiResponse(200, false, $message, null), 200);
			}

			$adminImage = checkFileExitOrNot($admin['image'], public_path(config('constants.admin_profile_img_path')));
			$adminImage = $adminImage === true
				? asset(config('constants.admin_profile_img_path') . '/' . $admin['image'])
				: asset(config('constants.no_user_image_path'));

			$response = [];
			$response['token'] = $token;
			$response['id'] = $admin['id'];
			$response['name'] = $admin['name'];
			$modules = AdminModule::with('hasChild')->where('parent_id', null);
			$modules = $modules->orderBy('sort_order');
			if ($admin->user_group_id != 1) {
				$modules = $modules->whereIn('slug', array_keys(json_decode($admin->usersGroup->permissions, true)));
			}
			$modules = $modules->get();

			$moduleArray = [];
			foreach ($modules as $module) {
				$moduleDetails['name'] = $module['module_name'];
				$moduleDetails['url'] = $module['slug'];
				$moduleDetails['children'] = [];
				if ($module->hasChild->count() > 0) {
					foreach ($module->hasChild as $child) {

						$childArray['name'] = $child['module_name'];
						$childArray['url'] = $child['slug'];
						$moduleDetails['children'][] = $childArray;
					}
				}
				$moduleArray[] = $moduleDetails;
			}
			$response['admin_module'] = $moduleArray;
			$response['status'] =  ($admin['status'] == 1) ? 'Online' : 'Offline';
			$response['adminImage'] = $adminImage;
			$admin->api_token = $token;
			$admin->save();
			$message = config('message.auth_message.user_logged_in_successfully');
			return response()->json(apiResponse(200, true, $message, $response), 200);
		} catch (\Exception $e) {
			Log::info([
				'Route' => $this->currentPath,
				'Request' => $request->all(),
				'Error' => $e->getMessage(),
				'Line' => $e->getLine()
			]);
			$message = config('message.common_message.exception_error');
			return response()->json(apiResponse(200, false, $message, null), 200);
		}
	}
	public function refreshToken(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				'token' => 'required',
			]);

			if ($validator->fails()) {
				return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
			}
			$token = JWTAuth::parseToken()->refresh();
			$data['token'] = $token;
			$message = config('message.auth_message.user_logged_in_successfully');
			return response()->json(apiResponse(200, true, $message, $data), 200);
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
	/**
	 * Log the user out (Invalidate the token).
	 * Logout : Logout window for User, it can be employer/candidate
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function adminLogout(Request $request)
	{
		try {
			Auth::guard('api')->logout();
			return response()->json(apiResponse(200, true, 'Admin successfully signed out', null), 200);
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

	public function adminPasswordChange(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				'current_password' => 'required',
				'new_password' => 'required|string|min:8',
				'confirm_password' => 'required|same:new_password',
			]);

			if ($validator->fails()) {
				return response()->json([
					'status_code' => 200, 'success' => false,
					'message' => $validator->errors(), 'data' => null
				], 200);
			}

			if (empty(auth()->user())) {
				return response([
					'status_code' => 200, 'success' => false,
					'message' => config('message.auth_message.admin_user_not_found'), 'data' => null
				], 200);
			}
			$adminUser = AdminUser::where('id', auth()->user()->id)->first();
			if (!(Hash::check(request('current_password'), $adminUser->password))) {
				return response([
					'status_code' => 200, 'success' => false,
					'message' => config('message.auth_message.wrong_password_error'), 'data' => null
				], 200);
			}
			if (strcmp($request->current_password, $request->new_password) == 0) {
				return response([
					'status_code' => 200, 'success' => false,
					'message' => config('message.auth_message.password_compare_error'), 'data' => null
				], 200);
			}

			//update admin_user password
			$adminUser->password = Hash::make($request->new_password);
			$adminUser->save();

			return response()->json([
				'status_code' => 200, 'success' => true,
				'message' => config('message.auth_message.please_login_again_with_new_password'), 'data' => null
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

	/**
	 * Get the broker to be used during password reset.
	 *
	 * @return \Illuminate\Contracts\Auth\PasswordBroker
	 */
	public function broker(): \Illuminate\Contracts\Auth\PasswordBroker
	{
		return Password::broker('admins');
	}

	public function adminSendResetLinkResponse(Request $request)
	{
		try {
			$input = $request->only('email');

			$validator = Validator::make($input, [
				'email' => "required|email"
			]);

			if ($validator->fails()) {
				$returnRresponse =  response([
					'status_code' => 200, 'success' => false,
					'message' => $validator->errors()->all(), 'data' => null
				], 200);
			} else {
				$adminUser = AdminUser::where('email', $request->email)->first();
				if ($adminUser == null) {
					$returnRresponse = response([
						'status_code' => 200, 'success' => false,
						'message' => config('message.auth_message.admin_user_not_found_with_given_email'), 'data' => null
					], 200);
				} else {
					$verifyToken = Str::random(64);
					$message = config('message.front_user.auth_mail_sent_succes');
					$returnRresponse = $this->generalServicesObj->sendForgotPasswordMail($adminUser, $verifyToken, $message);
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

	protected function adminSendResetResponse(Request $request)
	{
		try {
			$input = $request->only('token', 'password', 'confirm_password');
			$validator = Validator::make($input, [
				'token' => 'required',
				'password' => 'required|string|min:8',
				'confirm_password' => 'required|same:password',
			]);

			if ($validator->fails()) {
				return response([
					'status_code' => 200, 'success' => false,
					'message' => $validator->errors()->all(), 'data' => null
				], 200);
			}

			$adminUser = AdminUser::where('reset_password_token', $request->token)->first();

			if ($adminUser == null) {
				return response([
					'status_code' => 200, 'success' => false,
					'message' => config('message.admin_users.admin_user_not_found'), 'data' => null
				], 200);
			}

			$adminUser->forceFill(['password' => Hash::make($request->password)])->save();

			return response()->json([
				'status_code' => 200, 'success' => true,
				'message' => config('message.auth_message.password_reset_successfully'), 'data' => null
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

	# Verifing token in database
	public function adminVerifiedEmail(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				'token' => 'required',
				'flag' => 'required'
			]);

			if ($validator->fails()) {
				return response()->json([
					'status_code' => 200, 'success' => false,
					'message' => $validator->errors(), 'data' => null
				], 200);
			}

			if ($request->flag == 'password_reset') {

				$adminUser = AdminUser::where('reset_password_token', $request->token)->first();

				if ($adminUser == null) {
					return response()->json([
						'status_code' => 200, 'success' => false,
						'message' => config('message.auth_message.admin_user_not_found'), 'data' => null
					], 200);
				} else {
					return response()->json([
						'status_code' => 200, 'success' => true,
						'message' =>  config('message.auth_message.token_found_successfully'), 'data' => null
					], 200);
				}
			}
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
