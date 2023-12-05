<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Http\Controllers\Controller;
use App\Models\Admin\UsersGroup;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminUsersController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $adminUserList = AdminUser::select('admin_users.*');

            $adminUserList =  $adminUserList->where('admin_users.name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('admin_users.email', 'LIKE', '%' . $request->search . '%');

            $sort = (isset($request->sort) && isset($request->sort) != '')
                ? $request->sort : 'admin_users.id';
                
            $direction = (isset($request->direction) && isset($request->direction) != '')
                ? $request->direction : 'DESC';

            $adminUserList = $adminUserList->orderBy($sort, $direction);

            $adminUserList = $adminUserList->paginate(env('PAGINATION_COUNT'));
            $response = [];
            if ($adminUserList->count() > 0) {
                $page  = (!empty($request->page)) ?  $request->page : 1 ;
                
                foreach ($adminUserList as $k => $adminUser) {
                    $adminUsersArr = [];
                    $adminUsersArr['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                    $adminUsersArr['id'] = encryptId($adminUser->id);
                    $adminUsersArr['name'] = $adminUser->name;
                    $adminUsersArr['email'] = $adminUser->email;
                    $adminUsersArr['status'] = ($adminUser->status == 1) ?  'Active' : 'Inactive';
                    $response[] = $adminUsersArr;
                }
            }
            $paginateData = $adminUserList->toArray();
            $pagination =  pagination($paginateData);

            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_users.admin_users_fetched_successfully'),
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            //check validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:admin_users',
                'name' => 'required',
                'status' => 'required',
                'user_group_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }

            $postData = $request->only('email', 'name', 'status', 'user_group_id');
            $password = getUniqueId();
            $postData['password'] = Hash::make($password);
            $postData['status'] = $postData['status'] ?? 0;
            $postData['user_group_id'] = $postData['user_group_id'] ?? "1";
            $postData['user_type'] = !empty($postData['user_type']) ? $postData['user_type'] : '1';
            $data = AdminUser::create($postData);
            $mailBody = getTemplateInfo('new_admin_user', 'email', [
                '##NAME##' => $postData['name'] ?? '',
                '##EMAIL##' => $postData['email'] ?? '',
                '##PASSWORD##' => $password,
            ]);
            $mailData = [
                'message' => $mailBody['message'] ?? '',
                'subject' => $mailBody['subject'] ?? '',
            ];
            sendEmail($postData['email'] ?? '', $mailData);
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_users.admin_user_created_successfully'),
                'data' => $data
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUsers(Request $request, $id)
    {
        $id = decryptId($id) ?? 0;
        try {
            $usersDetails = AdminUser::find($id);

            if ($usersDetails == null) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' =>  config('message.admin_users.admin_user_not_found'),
                    'data' => $usersDetails
                ], 200);
            } else {
                return response()->json([
                    'status_code' => 200, 'success' => true,
                    'message' => config('message.admin_users.admin_users_fetched_successfully'),
                    'data' => $usersDetails
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
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            //check validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:admin_users,email,' . decryptId($request->id),
                'name' => 'required',
                'status' => 'required',
                'user_group_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }

            $postData = $request->only('id', 'name', 'email', 'status', 'user_group_id');
            $postData['id'] = decryptId($postData['id']);
            $postData['status'] = $postData['status'] ?? "0";
            $postData['user_group_id'] = $postData['user_group_id'] ?? "1";
            $postData['user_type'] = !empty($postData['user_type']) ? $postData['user_type'] : '1';
            $users = AdminUser::find($postData['id']);

            if ($users == null) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.admin_users.admin_user_not_found'), 'data' => $users
                ], 200);
            } else {
                $users->update($postData);
                return response()->json([
                    'status_code' => 200, 'success' => true,
                    'message' => config('message.admin_users.admin_user_updated_successfully'), 'data' => $users
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $id = decryptId($id) ?? 0;
        try {
            AdminUser::destroy($id);
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_users.admin_user_deleted_successfully'), 'data' => null
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

    public function getUserGroups(Request $request)
    {
        try {
            $response = [];
            $userGroups = UsersGroup::where('status', '1')->get();
            if (isset($userGroups) && !empty($userGroups)) {
                foreach ($userGroups as $val) {
                    $userGroupsArr = [];
                    $userGroupsArr['id'] = $val->id;
                    $userGroupsArr['group_name'] = $val->group_name;
                    $response[] = $userGroupsArr;
                }
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_users.user_groups_fetch_success'), 'data' => $response
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
