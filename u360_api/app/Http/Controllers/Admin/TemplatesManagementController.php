<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TemplatesManagementController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }
    public function index(Request $request)
    {
        try {
            $templateList = TemplateManagement::Query();
            (isset($request->name) && $request->name != '')
                ? $templateList = $templateList->where('name', 'LIKE', '%' . $request->name . '%')
                : '';

            (isset($request->status) && $request->status != '')
                ? $templateList =  $templateList->where('status', $request->status)
                : '';

            (isset($request->template_type) && $request->template_type != '')
                ? $templateList = $templateList->where('template_type', $request->template_type)
                : '';

            $sort =  (isset($request->sort) && isset($request->sort) != '') ? $request->sort : 'id';
            $direction =  (isset($request->direction) && isset($request->direction) != '')
                ? $request->direction : 'DESC';
            
            $templateList = $templateList->orderBy($sort, $direction);
            $templateList = $templateList->paginate(env('PAGINATION_COUNT'));

            $response = [];
            if ($templateList->count() > 0) {
                $page = (!empty($request->page)) ? $request->page : 1;
                foreach ($templateList as $k => $template) {
                    $templatesArr = [];
                    $templatesArr['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                    $templatesArr['id'] = encryptId($template->id);
                    $templatesArr['name'] = $template->name;
                    $templatesArr['slug'] = $template->slug;
                    $templatesArr['status'] = $template->status == 1 ? 'Active' : 'Inactive';
                    $templatesArr['template_type'] = $template->template_type;
                    $response[] = $templatesArr;
                }
            }
            $paginateData = $templateList->toArray();
            $pagination =  pagination($paginateData);

            // if no data found
            if ($templateList->count() == 0) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.common_message.error_message')
                ], 200);
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_template_management.fetch_success'),
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
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'slug' => 'required|unique:templates_management,slug',
                'template_type' => 'required',
                'template' => 'required',
                'status' => 'required',
                'subject' => 'required_if:template_type,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }
            $user = jwtAuthUser();
            $postData = $request->all();
            $templateObj = new TemplateManagement();
            $templateObj->name = $postData['name'] ?? '';
            $templateObj->slug = slugGenerator($postData['slug']) ?? '';
            $templateObj->template_type = $postData['template_type'] ?? 'email';
            $templateObj->template = $postData['template'] ?? '';
            $templateObj->subject = $postData['subject'] ?? '';
            $templateObj->status = $postData['status'] ?? 0;
            $templateObj->created_by = $user->id ?? 0;
            $templateObj->save();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_template_management.added_success'), 'data' => null
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

    public function edit(Request $request, $id)
    {
        try {
            $id = decryptId($id) ?? 0;
            $template = TemplateManagement::find($id);
            if (empty($template)) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.admin_template_management.not_found'), 'data' => null
                ], 200);
            }
            $templateArray = $template->toArray();
            $response = [];
            $response['templateDetails']['id'] = $templateArray['id'] ?? 0;
            $response['templateDetails']['name'] = $templateArray['name'] ?? '';
            $response['templateDetails']['slug'] = $templateArray['slug'] ?? '';
            $response['templateDetails']['template_type'] = $templateArray['template_type'] ?? 'email';
            $response['templateDetails']['template'] = $templateArray['template'] ?? '';
            $response['templateDetails']['subject'] = $templateArray['subject'] ?? '';
            $response['templateDetails']['status'] = $templateArray['status'] ?? 0;
            $response['templateDetails']['created_by'] = $templateArray['created_by'] ?? 0;
            $response['templateDetails']['updated_by'] = $templateArray['updated_by'] ?? 0;
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_template_management.fetch_success'), 'data' => $response
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

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required',
                'slug' => 'required|unique:templates_management,slug,' . decryptId($request->id),
                'template_type' => 'required',
                'template' => 'required',
                'status' => 'required',
                'subject' => 'required_if:template_type,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }
            $user = jwtAuthUser();
            $postData = $request->all();
            $templateId = decryptId($postData['id']) ?? 0;
            $templateObj = TemplateManagement::find($templateId);
            if (!isset($templateObj) && empty($templateObj)) {
                return response()->json([
                    'status_code' => 500, 'success' => false,
                    'message' => config('message.admin_template_management.not_found'), 'data' => null
                ], 500);
            }
            $templateObj->name = $postData['name'] ?? '';
            $templateObj->slug = slugGenerator($postData['slug']) ?? '';
            $templateObj->template_type = $postData['template_type'] ?? 'email';
            $templateObj->template = $postData['template'] ?? '';
            $templateObj->subject = $postData['subject'] ?? '';
            $templateObj->status = $postData['status'] ?? 0;
            $templateObj->updated_by = $user->id ?? 0;
            $templateObj->save();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_template_management.updated_success'), 'data' => null
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
            $templateExist = TemplateManagement::find($id);
            if (empty($templateExist)) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.admin_template_management.not_found'), 'data' => null
                ], 200);
            }
            $templateExist->delete();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_template_management.delete_success'), 'data' => null
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
