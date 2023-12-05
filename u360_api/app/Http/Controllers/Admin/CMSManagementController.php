<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\CmsManagement;
use Illuminate\Support\Facades\Route;

class CMSManagementController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }
    public function index(Request $request)
    {
        try {
            $cmsList = CmsManagement::Query();

            (isset($request->name) && $request->name != '') ?
                $cmsList = $cmsList->where('name', 'LIKE', '%' . $request->name . '%') : '';

            (isset($request->status) && $request->status != '') ?
                $cmsList = $cmsList->Where('status', $request->status) : '';

            $sort  = (isset($request->sort) && isset($request->sort) != '') ?
                $request->sort : 'id';

            $direction =  (isset($request->direction) && $request->direction != '') ?
                $request->direction : 'DESC';

            $cmsList = $cmsList->orderBy($sort, $direction);
            $cmsList = $cmsList->paginate(env('PAGINATION_COUNT'));

            $response = [];
            if ($cmsList->count() > 0) {
                $page = !empty($request->page) ? $request->page : 1;
                foreach ($cmsList as $k => $cms) {
                    $cmsArr = [];
                    $cmsArr['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                    $cmsArr['id'] = encryptId($cms->id);
                    $cmsArr['name'] = $cms->name;
                    $cmsArr['slug'] = $cms->slug;
                    $cmsArr['status'] = $cms->status == 1 ? 'Active' : 'Inactive';
                    $response[] = $cmsArr;
                }
            }
            $paginateData = $cmsList->toArray();
            $pagination =  pagination($paginateData);

            // if no data found
            if ($cmsList->count() == 0) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => config('message.common_message.error_message'),
                    'data' => compact('response', 'pagination')
                ], 200);
            }

            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_cms_management.fetch_success'),
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
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'slug' => 'required|unique:cms_management,slug',
                'content' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }
            $user = jwtAuthUser();
            $postData = $request->all();
            $cmsObj = new CmsManagement();
            $cmsObj->name = $postData['name'] ?? '';
            $cmsObj->slug = slugGenerator($postData['slug']) ?? '';
            $cmsObj->content = $postData['content'] ?? '';
            $cmsObj->status = $postData['status'] ?? 0;
            $cmsObj->created_by = $user->id ?? 0;
            $cmsObj->save();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_cms_management.added_success'),
                'data' => null
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

    public function edit(Request $request, $id)
    {
        try {
            $id = decryptId($id) ?? 0;
            $cms = CmsManagement::find($id);
            if (empty($cms)) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.admin_cms_management.not_found'),
                    'data' => null
                ], 200);
            }
            $cmsArray = $cms->toArray();
            $response = [];
            $response['cmsDetails']['id'] = $cmsArray['id'] ?? 0;
            $response['cmsDetails']['name'] = $cmsArray['name'] ?? '';
            $response['cmsDetails']['slug'] = $cmsArray['slug'] ?? '';
            $response['cmsDetails']['content'] = $cmsArray['content'] ?? '';
            $response['cmsDetails']['status'] = $cmsArray['status'] ?? 0;
            $response['cmsDetails']['created_by'] = $cmsArray['created_by'] ?? 0;
            $response['cmsDetails']['updated_by'] = $cmsArray['updated_by'] ?? 0;
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_cms_management.fetch_success'),
                'data' => $response
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

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required',
                'slug' => 'required|unique:cms_management,slug,' . decryptId($request->id),
                'content' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }

            $user = jwtAuthUser();
            $postData = $request->all();
            $cmsId = decryptId($postData['id']) ?? 0;
            $cmsObj = CmsManagement::find($cmsId);
            if (!isset($cmsObj) && empty($cmsObj)) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.admin_cms_management.not_found'),
                    'data' => null
                ], 500);
            }
            $cmsObj->name = $postData['name'] ?? '';
            $cmsObj->slug = slugGenerator($postData['slug']) ?? '';
            $cmsObj->content = $postData['content'] ?? '';
            $cmsObj->status = $postData['status'] ?? 0;
            $cmsObj->updated_by =  $user->id ?? 0;
            $cmsObj->save();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_cms_management.updated_success'),
                'data' => null
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

    public function delete(Request $request, $id)
    {
        try {
            $id = decryptId($id) ?? 0;
            $cmsExist = CmsManagement::find($id);
            if (empty($cmsExist)) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.admin_cms_management.not_found'),
                    'data' => null
                ], 200);
            }
            $cmsExist->delete();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_cms_management.delete_success'),
                'data' => null
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

    /* this funcation is used for get the
     *  cms pages details request as slug
     */
    public function getCmsPage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'slug' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }
            $input = $request->all();
            $getPage = CmsManagement::select('id', 'name', 'slug', 'content', 'status')
                ->where(['status' => '1', 'slug' => $input["slug"]])->first();

            $response = [];
            if ($getPage) {
                $response = $getPage;

                return response()->json(
                    apiResponse(
                        200,
                        true,
                        config('message.admin_cms_management.cms_page_fetch_successfully'),
                        $response
                    ),
                    200
                );
            } else {
                return response()->json(
                    apiResponse(
                        200,
                        false,
                        config('message.admin_cms_management.cms_page_not_fetch'),
                        $response
                    ),
                    200
                );
            }
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }
}
