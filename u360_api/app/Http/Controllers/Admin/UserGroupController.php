<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\UsersGroup;
use App\Http\Controllers\Controller;
use App\Models\AdminModule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserGroupController extends Controller
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
            $userGroupList = new UsersGroup;
            $filter = $request->all();

            $sort  = (isset($request->sort) && isset($request->sort) != '') ? $request->sort : 'id';
            $direction  = (isset($request->direction) && isset($request->direction) != '')
                ? $request->direction : 'ASC';

            $userGroupList = $userGroupList->orderBy($sort, $direction);

            (isset($filter['filter_name']) && $filter['filter_name'] != '')
                ? $userGroupList = $userGroupList->where('group_name', 'LIKE', '%' . $filter['filter_name'] . '%')
                : '';

            (isset($filter['status']) && $filter['status'] != '')
                ? $userGroupList = $userGroupList->where('status', $filter['status']) : '';
                
            (isset($filter['id']) && $filter['id'] != '')
                ? $userGroupList = $userGroupList->where('id', $filter['id']) : '';

            $userGroupList = $userGroupList->paginate(env('PAGINATION_COUNT'));

            $response = [];
            if ($userGroupList->count() > 0) {
                foreach ($userGroupList as $userGroup) {
                    $userGroupsArr = [];
                    $userGroupsArr['id'] = encryptId($userGroup->id);
                    $userGroupsArr['group_name'] = $userGroup->group_name;
                    $userGroupsArr['description'] = $userGroup->description;
                    $userGroupsArr['status'] = ($userGroup->status == 1) ? 'Active' : 'Inactive';
                    $userGroupsArr['permissions'] = $userGroup->permissions;
                    $userGroupsArr['created_at'] = $userGroup->created_at;
                    $userGroupsArr['updated_at'] = $userGroup->updated_at;
                    $response[] = $userGroupsArr;
                }
            }
            $paginateData = $userGroupList->toArray();
            $pagination =  pagination($paginateData);

            // if no data found
            if ($userGroupList->count() == 0) {
                return response()->json([
                    'status_code' => 200, 'success' => true,
                    'message' => config('message.common_message.error_message')
                ], 200);
            }

            //success
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.user_group_admin.userGroups_fetched_successfully'),
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
    public function store(Request $request)
    {
        try {
            //check validation
            $validator = Validator::make($request->all(), [
                'group_name' => 'required|unique:user_groups',
                'permissions' => 'required',
                'status' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }

            $postData = $request->only('group_name', 'description', 'status', 'permissions', 'year_group');
            $postData['status'] = (isset($postData['status']) && $postData['status'] == '1') ? '1' : '0';
            $data = UsersGroup::create($postData);
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.user_group_admin.userGroups_created_successfully'), 'data' => $data
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
    public function getUserGroup(Request $request, $id)
    {
        try {
            $id = decryptId($id);
            $userGroupDetails = UsersGroup::find($id);

            if ($userGroupDetails == null) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.user_group_admin.userGroup_not_found'),
                    'data' => $userGroupDetails
                ], 200);
            }
            $userGroupDetailsArray = $userGroupDetails->toArray();
            $permissionArray = json_decode($userGroupDetailsArray['permissions'], true);

            $response = [];
            $response['id'] = $userGroupDetailsArray['id'];
            $response['group_name'] = $userGroupDetailsArray['group_name'];
            $response['description'] = $userGroupDetailsArray['description'];
            $response['status'] = $userGroupDetailsArray['status'];
            $response['year_group'] = $userGroupDetailsArray['year_group'];
            if ($permissionArray != null) {
                foreach ($permissionArray as $key => $value) {
                    foreach ($value as $subvalue) {
                        $keyName =  $key . '-' . $subvalue;
                        $result[$keyName] =  $subvalue;
                        $response['permission'] = (object)($result);
                    }
                }
            } else {
                $response['permission'] = '';
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.user_group_admin.userGroup_fetched_successfully'), 'data' => $response
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
            $id = decryptId($request->id);
            $validator = Validator::make($request->all(), [
                'group_name' => 'required|unique:user_groups,group_name,' . $id,
                'permissions' => 'required',
                'status' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }

            $postData = $request->only('group_name', 'description', 'status', 'permissions', 'year_group');
            $postData['status'] = (isset($postData['status']) && $postData['status'] == '1') ? '1' : '0';
            $userGroup = UsersGroup::find($id);

            if ($userGroup == null) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.user_group_admin.userGroup_not_found'), 'data' => $userGroup
                ], 200);
            } else {
                $userGroup->update($postData);
                return response()->json([
                    'status_code' => 200, 'success' => true,
                    'message' => config('message.user_group_admin.userGroup_updated_successfully'), 'data' => $userGroup
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
        try {
            $id = decryptId($id);
            UsersGroup::destroy($id);
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.user_group_admin.userGroup_deleted_successfully'), 'data' => null
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

    public function userGroupList(Request $request)
    {
        try {
            $adminModuleList = AdminModule::get();
            $response = [];
            if ($adminModuleList->count() > 0) {
                foreach ($adminModuleList as $adminModule) {
                    $userGroupsArr = [];
                    $userGroupsArr['module_name'] = $adminModule->module_name;
                    $userGroupsArr['slug'] = $adminModule->slug;
                    $userGroupsArr['action'] = json_decode($adminModule->action);
                    $response[] = $userGroupsArr;
                }
            }

            // if no data found
            if ($adminModuleList->count() == 0) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.common_message.error_message')
                ], 200);
            }

            //success
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.user_group_admin.userGroups_fetched_successfully'),
                'data' => compact('response')
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
