<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class SiteSettingsController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    public function store(Request $request)
    {
        try {
            $user = jwtAuthUser();
            $postData = $request->all();
            $message = 'add_success';
            foreach ($postData as $key => $keyValue) {
                $siteSettingArr = ['key_value' => $keyValue];
                $exist = SiteSetting::where('key_name', $key)->where('key_value', $keyValue)->first();
                if (isset($exist) && !empty($exist)) {
                    $siteSettingArr['updated_by'] = $user->id;
                    $message = 'update_success';
                } else {
                    $siteSettingArr['created_by'] = $user->id;
                    $message = 'add_success';
                }
                SiteSetting::updateOrCreate(
                    ['key_name' => $key],
                    $siteSettingArr
                );
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.admin_site_settings.' . $message), 'data' => null
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

    public function getSiteSettings(Request $request)
    {
        try {
            $siteSettings = SiteSetting::Query();
            (isset($request->search) && $request->search != '')
                ? $siteSettings = $siteSettings->where('key_name', 'like', '%' . $request->search . '%')
                : '';
            $siteSettings = $siteSettings->get();
            if (isset($siteSettings) && !empty($siteSettings) && $siteSettings->count() > 0) {
                $response = $mainSiteSettingsArr = [];
                foreach ($siteSettings as $siteSetting) {
                    $siteSettingArr = [];
                    $siteSettingArr['key'] = $siteSetting->key_name ?? '';
                    $siteSettingArr['value'] = $siteSetting->key_value ?? '';
                    $mainSiteSettingsArr[] = $siteSettingArr;
                }
                $response['siteSettings'] = $mainSiteSettingsArr;
                return response()->json([
                    'status_code' => 200, 'success' => true,
                    'message' => config('message.admin_site_settings.fetch_success'), 'data' => $response
                ], 200);
            } else {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.admin_site_settings.not_found'), 'data' => null
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
