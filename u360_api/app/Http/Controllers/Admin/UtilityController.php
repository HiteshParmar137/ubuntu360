<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\UsersGroup;
use App\Models\AdminModule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UtilityController extends Controller
{
	protected $currentPath;
	public function __construct()
	{
		$this->currentPath = Route::getFacadeRoot()->current()->uri();
	}

	public function getMenus(Request $request)
	{
		try {
			$user = jwtAuthUser();
			$userGroupDetails = UsersGroup::find($user->user_group_id);
			$allowedModule = json_decode($userGroupDetails->permissions, true);
			if ($user->user_group_id !== 1) {
				$allowedModule = array_keys($allowedModule);
			} elseif ($allowedModule == null || $allowedModule == '' || $allowedModule === false) {
				$allowedModule = [];
			} else {
				$allowedModule = array_keys($allowedModule);
			}
			return response()->json([
				'status_code' => 200, 'success' => true,
				'message' => config('message.utility_admin.menus_get_succesfully'),
				'data' => $allowedModule
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
				'message' => config('message.common_message.exception_error'),
				'data' => null
			], 500);
		}
	}

	public function getSystemModules(Request $request)
	{
		try {
			$systemModule = AdminModule::select(['module_name', 'action', 'slug'])->get()->toArray();
			$response = [];
			foreach ($systemModule as $value) {
				$systemModulseArr = [];
				$systemModulseArr['module_name'] =  $value['module_name'];
				$systemModulseArr['action'] =  json_decode($value['action']);
				$systemModulseArr['slug'] =  $value['slug'];
				$response[] = $systemModulseArr;
			}

			return response()->json([
				'status_code' => 200, 'success' => true,
				'message' => config('message.utility_admin.menus_get_succesfully'), 'data' => $response
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

	public function getSidebar(Request $request)
	{
		try {
			$systemModule = AdminModule::select(['module_name as name', 'slug as url'])
				->orderBy('sort_order', 'ASC')->get()->toArray();
			return response()->json([
				'status_code' => 200, 'success' => true,
				'message' => config('message.utility_admin.menus_get_succesfully'), 'data' => $systemModule
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

	public function checkPermissions(Request $request)
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				return response()->json([
					'status_code' => 200, 'success' => false,
					'message' =>  config('message.auth_message.user_not_found'), 'data' => null
				], 200);
			}
			$user = jwtAuthUser();

			$userGroupDetails = UsersGroup::find($user->user_group_id);
			$permissions = '';
			if ($userGroupDetails->id != 1 && isset($userGroupDetails->permissions) && $userGroupDetails->permissions != null) {

				$validator = Validator::make($request->all(), [
					'module_name' => 'required', //user-group
					'action' => 'required' //add
				]);

				if ($validator->fails()) {
					return response()->json([
						'status_code' => 200, 'success' => false,
						'message' => $validator->errors(), 'data' => null
					], 200);
				}

				$actionModule = $request->module_name;
				$actionType = $request->action;

				$permissions = json_decode($userGroupDetails->permissions, true);

				if (sizeof($permissions) > 0) {
					$permissionModule = array_keys($permissions);
					if (!in_array($actionModule, $permissionModule)) {
						return response()->json([
							'status_code' => 200, 'success' => false,
							'message' => config('message.utility_admin.permission_denied_for_this_operation'), 'data' => null
						], 200);
					}
					if (!in_array($actionType, $permissions[$actionModule])) {
						return response()->json([
							'status_code' => 200, 'success' => false,
							'message' => config('message.utility_admin.permission_denied_for_this_operation'), 'data' => null
						], 200);
					} else {
						return response()->json([
							'status_code' => 200, 'success' => true,
							'message' => config('message.utility_admin.permission_granted'), 'data' => null
						], 200);
					}
				} else {
					return response()->json([
						'status_code' => 200, 'success' => false,
						'message' => config('message.utility_admin.not_found_any_permission'),
						'data' => null
					], 200);
				}
			} elseif (
				$userGroupDetails->id != 1 && !isset($userGroupDetails->permissions)
				&& $userGroupDetails->permissions == null
			) {
				return response()->json([
					'status_code' => 200, 'success' => false,
					'message' => config('message.utility_admin.contact_super_user'), 'data' => null
				], 200);
			} elseif ($userGroupDetails->id == 1) {
				return response()->json([
					'status_code' => 200, 'success' => true,
					'message' => config('message.utility_admin.permission_granted'), 'data' => null
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
				'status_code' => 500, 'success' => false,
				'message' => config('message.common_message.exception_error'), 'data' => null
			], 500);
		}
	}
}
