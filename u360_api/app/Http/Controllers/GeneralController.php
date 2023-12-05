<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\Industry;
use App\Models\Sdg;
use App\Models\UserInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class GeneralController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    public function getCategoriesList(Request $request)
    {
        try {
            $categories = Category::select('id', 'name')->get()->toArray();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.general_msg.categories_fetch_success'),
                'data' => $categories
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

    public function getSdgsList(Request $request)
    {
        try {
            $sdgs = Sdg::select('id', 'name', 'icon','icon_color')->get()->toArray();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.general_msg.sdgs_fetch_success'), 'data' => $sdgs
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
    public function getUserInterestList(Request $request)
    {
        try {
            $sdgs = UserInterest::select('id', 'name')->get()->toArray();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.general_msg.category_fetch_success'), 'data' => $sdgs
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
    public function getCountriesList(Request $request)
    {
        try {
            $countries = Country::select('id', 'name', 'phone')->get()->toArray();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.general_msg.countries_fetch_success'), 'data' => $countries
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
    public function getIndustryList(Request $request)
    {
        try {
            $industries = Industry::select('id', 'name')->get()->toArray();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.general_msg.industries_fetch_success'),
                'data' => $industries
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
