<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\AppUserDetail;
use App\Models\AppUserInterest;
use App\Models\AppUserSdg;
use App\Models\DeviceToken;
use App\Models\User;
use App\Models\TemplateManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
class UserRegistrationController extends Controller
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    public function registerUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => [
                    'required',
                    'string',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                ],
                'confirm_password' => 'required_if:social_id,=,null|same:password',
                'social_id' =>  "required_if:password,=,null",
                'device_type' => 'required|numeric',
                'device_token' => 'required_if:device_type,1|required_if:device_type,2',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }
            DB::beginTransaction();
            $postData = $request->only('name', 'email', 'password', 'device_type', 'device_token', 'social_id');
            $response = [];
            $name = $postData['name'] ?? '';
            $email = $postData['email'] ?? '';
            $password = $postData['password'] ?? '';
            $socialId = $postData['social_id'] ?? '';
            $verifyToken = (empty($socialId)) ? Str::random(64) : '';

            $userArray = [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'social_id' => $socialId,
                'email_verify_token' => $verifyToken
            ];
            $user = User::create($userArray);

            if (isset($postData['device_type']) && isset($postData['device_token'])) {
                $deviceTokenArr = [
                    'user_id' => $user['id'] ?? '',
                    'device_type' => $postData['device_type'] ?? 0,
                    'device_token' => $postData['device_token'] ?? '',
                ];
                DeviceToken::create($deviceTokenArr);
            }

            $token = JWTAuth::attempt([
                'email' => $email,
                'password' => $password,
            ]);
            $frontUrl = env('FRONT_URL');
            $url =  $frontUrl . "verify-email/" . $verifyToken;
            $emailTemplate = TemplateManagement::where('slug', 'signup_email_verify')->first();
            $message = '';
            if (!empty($emailTemplate)) {
                $message = str_replace('##URL##', $url, $emailTemplate->template);
                $message = str_replace('##NAME##', $name, $message);
            }
            $details = [
                'subject' => 'Verify Email Address',
                'message' => $message,
            ];

            Mail::send('emails.mail', ['mailData' => $details], function ($message) use ($email, $details) {
                $message->to(convertStringToLowercase($email));
                $message->subject($details['subject']);
            });
            $response['userDetails']['id'] = encryptId($user->id);
            $response['userDetails']['name'] = $name;
            $response['userDetails']['email'] = $email;
            $response['userDetails']['token'] = $token ?? '';
            DB::commit();
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.front_user.registered_success'), 'data' => $response
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
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
    public function verifyEmail(Request $request)
    {
        $verifyUser = User::where('email_verify_token', $request->token)->first();

        $message = config('message.front_user.in_valid_verify_token');

        if (!is_null($verifyUser)) {
            if (!$verifyUser->is_email_verified) {
                $verifyUser->is_email_verified = 1;
                $verifyUser->save();
                $message = config('message.front_user.user_email_verify_successfully');
            } else {
                $message = config('message.front_user.user_email_already_verfify');
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => $message, 'data' => null
            ], 200);
        }

        return response()->json(['status_code' => 200, 'success' => false, 'message' => $message, 'data' => null], 200);
    }

    public function userDetailsSave(Request $request, $stepType, $stepNo)
    {
        try {
            $fillInput = [];
            $user = jwtAuthUser();
            if ($stepNo > 0) {
                $validationArray = [
                    'name' => 'required|string',
                    'dob' => 'required',
                    'location' => 'required',
                    'contact_number' => 'required|numeric|digits_between:6,11',
                    'sponsor_type' => 'required|numeric',
                    'phone_code' => 'required|string',
                ];
                $fillInput = ['name', 'dob', 'location', 'phone_code','contact_number', 'sponsor_type'];
            }
            if ($stepNo > 1) {
                $postData = $request->all();
                $sponsorType = $postData['sponsor_type'] ?? 1;
                if ($sponsorType && $sponsorType == '2') {
                    $validationArray['corporation_name'] = 'required|string';
                    $validationArray['industry'] = 'required|string';
                    $validationArray['city'] = 'required|string';
                    $validationArray['country'] = 'required|string';
                    $validationArray['contact_name'] = 'required|string';
                    $validationArray['position'] = 'required|string';
                    $corporationInput = [
                        'corporation_name', 'industry', 'city',
                        'country', 'contact_name', 'position', 'other_industry'
                    ];
                    if (
                        !empty($postData['other_industry']) &&
                        $postData['industry'] == ''
                    ) {
                        $validationArray['other_industry'] = 'required|string';
                    }
                    $fillInput = array_merge($fillInput, $corporationInput);
                }
            }
            if ($stepNo == 3) {
                $validationArray['sdg_ids'] = 'required';
                $sdgInput = ['sdg_ids'];
                $fillInput = array_merge($fillInput, $sdgInput);
            }
            if ($stepNo == 4) {
                $socialInput = ['twitter', 'facebook', 'linkedin', 'instagram', 'snapchat', 'tiktok'];
                $fillInput = array_merge($fillInput, $socialInput);
            }
            if ($stepNo == 5) {
                if(empty($request->is_skip)){
                    $validationArray['interest_ids'] = 'required';
                    $sdgInput = ['interest_ids'];
                    $fillInput = array_merge($fillInput, $sdgInput);
                }
                $user->last_logged_in = date('Y-m-d H:i:s');
                $user->save();
            }
            $validator = Validator::make($request->all(), $validationArray);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => $validator->errors(), 'data' => null
                ], 200);
            }

            $postData = $request->only($fillInput);
            if (!empty($postData['industry']) && $postData['industry'] == 'other') {
                $postData['industry'] = null;
            }
            $userArray = [
                'name'     => $postData['name'],
                'user_type' => $postData['sponsor_type'],
            ];
            $userArray['is_signup_completed'] = ($stepType == 'submit') ? 1 : '';
            $postData['dob'] = changeDobDate($postData['dob']);
            $user->update($userArray);
            if (!empty($postData['sdg_ids'])) {
                $sdgIds = explode(',', $postData['sdg_ids']);
                $userSdgIds = AppUserSdg::where('user_id', $user->id)->pluck('sdg_id')->toArray();
                foreach ($userSdgIds as $sdgId) {
                    (!in_array($sdgId, $sdgIds)) ?
                        AppUserSdg::where('user_id', $user->id)->where('sdg_id', $sdgId)->delete()
                        : '';
                }
                foreach ($sdgIds as $sdgId) {
                    if (!in_array($sdgId, $userSdgIds)) {
                        $userSdgArray = ['user_id' => $user->id, 'sdg_id' => $sdgId];
                        AppUserSdg::insert($userSdgArray);
                    }
                }
            }
            if (!empty($postData['interest_ids'])) {
                $interestIds = explode(',', $postData['interest_ids']);
                $userInterestIds = AppUserInterest::where('user_id', $user->id)->pluck('interest_id')->toArray();
                foreach ($userInterestIds as $interestId) {
                    (!in_array($interestId, $interestIds))
                        ? AppUserInterest::where('user_id', $user->id)->where('interest_id', $interestId)->delete()
                        : '';
                }
                foreach ($interestIds as $interestId) {
                    if (!in_array($interestId, $userInterestIds)) {
                        $userInterestArray = ['user_id' => $user->id, 'interest_id' => $interestId];
                        AppUserInterest::insert($userInterestArray);
                    }
                }
            }
            AppUserDetail::updateOrCreate(
                ['user_id' => $user->id],
                $postData
            );
            $response = [];
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.front_user.user_details_success'), 'data' => $response
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 200, 'success' => false,
                'message' => config('message.common_message.exception_error'), 'data' => null
            ], 200);
        }
    }

    public function userDetails(Request $request)
    {
        try {
            $user = jwtAuthUser();
            $user = User::where('id', $user->id)->with('hasUserDetails', 'hasSdgsDetails')->first();
            $response = User::getUserDetails($user);
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.front_user.user_details_fetch_success'),
                'data' =>  $response
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 200, 'success' => false,
                'message' => config('message.common_message.exception_error'), 'data' => null
            ], 200);
        }
    }

    public function userDetailsUpdate(Request $request)
    {
        try {
            $user = jwtAuthUser();
            $fillInput = [];
            $postData = $request->all();

            $validationArray = [];
            if ($user->user_type == '2' && $postData['field_type'] == 'corpo_info') {
                $validationArray['corporation_name'] = 'required|string';
                $validationArray['industry'] = 'required|string';
                $validationArray['city'] = 'required|string';
                $validationArray['country'] = 'required|string';
                $validationArray['contact_name'] = 'required|string';
                $validationArray['position'] = 'required|string';
                $corporationInput = ['corporation_name', 'industry', 'city', 'country', 'contact_name', 'position'];
                $fillInput = array_merge($fillInput, $corporationInput);
                $userArray = [
                    'corporation_name'     => $postData['corporation_name'],
                    'industry'    => $postData['industry'],
                    'city' => $postData['city'],
                    'country' => $postData['country'],
                    'contact_name' => $postData['contact_name'],
                    'position' => $postData['position'],
                    'other_industry' => !empty($postData['other_industry']) ?
                        $postData['other_industry'] : ''
                ];
                if (
                    !empty($postData['other_industry']) &&
                    $postData['industry'] == ''
                ) {
                    $validationArray['other_industry'] = 'required|string';
                }
            } elseif ($postData['field_type'] == 'personal_info') {
                $validationArray = [
                    'name' => 'required|string',
                    'dob' => 'required',
                    'location' => 'required',
                    'contact_number' => 'required|numeric|digits_between:10,15',
                    'phone_code' => 'required',
                ];
                $fillInput = ['name', 'dob', 'location', 'contact_number', 'phone_code'];
                $userArray = [
                    'name'     => $postData['name'],
                    'location' => $postData['location'],
                    'contact_number' => $postData['contact_number'],
                    'dob' => $postData['dob'],
                    'phone_code' => $postData['phone_code']
                ];
            }

            switch ($postData['field_type']) {
                case 'about':
                    $validationArray['about'] = 'required';
                    $fillInput = ['about'];
                    $userArray = [
                        'about' => $postData['about']
                    ];
                    break;
                case 'sdg_ids':
                    $validationArray['sdg_ids'] = 'required';
                    $userArray = [];
                    $fillInput = [];
                    break;
                case 'interest_ids':
                    $validationArray['interest_ids'] = 'required';
                    $interestIds = $postData['interest_ids'];
                    $userArray = [];
                    $fillInput = [];
                    break;
                case 'social':
                    $fillInput = ['twitter', 'facebook', 'linkedin', 'instagram', 'snapchat', 'tiktok'];
                    $userArray = [
                        'twitter' => $postData['twitter'],
                        'facebook' => $postData['facebook'],
                        'linkedin' => $postData['linkedin'],
                        'instagram' => $postData['instagram'],
                        'snapchat' => $postData['snapchat']
                    ];
                    break;
                default:
                    $fillInput = [];
                    $userArray = [];
            }

            $validator = Validator::make($request->all(), $validationArray);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => $validator->errors(),
                    'data' => null
                ], 200);
            }
            if (!empty($fillInput)) {
                $postData = $request->only($fillInput);
            }

            if (!empty($postData['dob'])) {
                $postData['dob'] = changeDobDate($postData['dob']);
            }
            if (!empty($postData['industry']) && $postData['industry'] == 'other') {
                $postData['industry'] = null;
            }
            $user->update($userArray);

            if (!empty($postData['sdg_ids'])) {
                $sdgIds = explode(',', $postData['sdg_ids']);
                $userSdgIds = AppUserSdg::where('user_id', $user->id)->pluck('sdg_id')->toArray();
                foreach ($userSdgIds as $sdgId) {
                    (!in_array($sdgId, $sdgIds))
                        ? AppUserSdg::where('user_id', $user->id)->where('sdg_id', $sdgId)->delete()
                        : '';
                }
                foreach ($sdgIds as $sdgId) {
                    if (!in_array($sdgId, $userSdgIds)) {
                        $userSdgArray = ['user_id' => $user->id, 'sdg_id' => $sdgId];
                        AppUserSdg::insert($userSdgArray);
                    }
                }
            }
            if (!empty($postData['interest_ids'])) {
                $interestIds = explode(',', $postData['interest_ids']);
                $userInterestIds = AppUserInterest::where('user_id', $user->id)->pluck('interest_id')->toArray();
                foreach ($userInterestIds as $interestId) {
                    (!in_array($interestId, $interestIds))
                        ? AppUserInterest::where('user_id', $user->id)->where('interest_id', $interestId)->delete()
                        : '';
                }
                foreach ($interestIds as $interestId) {
                    if (!in_array($interestId, $userInterestIds)) {
                        $userInterestArray = ['user_id' => $user->id, 'interest_id' => $interestId];
                        AppUserInterest::insert($userInterestArray);
                    }
                }
            }
            AppUserDetail::updateOrCreate(
                ['user_id' => $user->id],
                $postData
            );
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.front_user.user_details_update_success'),
                'data' => $postData
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 200,
                'success' => false,
                'message' =>  $e->getMessage(),
                'data' => $postData
            ], 200);
        }
    }
}
