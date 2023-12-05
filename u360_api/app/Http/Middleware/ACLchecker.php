<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Admin\UsersGroup;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class ACLchecker
{
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['status_code' => 200, 'success' => false, 'message' =>  config('message.auth_message.user_not_found'), 'data' => null], 200);
            }
            $user =jwtAuthUser();
            $userGroupDetails = UsersGroup::find($user->user_group_id);
            $permissions = '';
            if ($userGroupDetails->id != 1 && isset($userGroupDetails->permissions) && $userGroupDetails->permissions != null) {

                if ($request->header('action-module') == null || $request->header('action-type') == null) {
                    return response()->json(['status_code' => 200, 'success' => false, 'message' =>   config('message.acl_checker.add_action_module_and_type_header'), 'data' => null], 200);
                }
                $actionModule = $request->header('action-module');
                $actionType = $request->header('action-type');

                $permissions = json_decode($userGroupDetails->permissions, true);

                if (sizeof($permissions) > 0) {
                    $permissionModule = array_keys($permissions);
                    if (!in_array($actionModule, $permissionModule)) {
                        return response()->json(['status_code' => 403, 'success' => false, 'message' => config('message.acl_checker.permission_denied_for_this_operation'), 'data' => null], 403);
                    }

                    if (!in_array($actionType, $permissions[$actionModule])) {
                        return response()->json(['status_code' => 403, 'success' => false, 'message' => config('message.acl_checker.permission_denied_for_this_operation'), 'data' => null], 403);
                    }
                } else {
                    return response()->json(['status_code' => 200, 'success' => false, 'message' => config('message.acl_checker.not_found_any_permission'), 'data' => null], 200);
                }
            } else if ($userGroupDetails->id != 1 && !isset($userGroupDetails->permissions) && $userGroupDetails->permissions == null) {
                return response()->json(['status_code' => 200, 'success' => false, 'message' =>  config('message.acl_checker.not_found_permission_contact_super_user'), 'data' => null], 200);
            }
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json(['status_code' => 500, 'success' => false, 'message' => config('message.common_message.exception_error'), 'data' => null], 500);
        }

        return $next($request);
    }
}
