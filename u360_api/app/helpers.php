<?php

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use App\Models\TemplateManagement;
use Illuminate\Support\Facades\Mail;
use App\Mail\DefaultMail;
use App\Models\Country;
use Illuminate\Support\Facades\Config;
use App\Models\DeviceToken;
use App\Models\Sdg;

function dateformat($date = '')
{
    if ($date && $date != '') {
        return date('d/m/Y', strtotime($date));
    }
    return '';
}
function changeDateFormat($date = '', $format = '')
{
    if ($date && $date != '' && !empty($format)) {
        return date($format, strtotime($date));
    }
    return '';
}
function changeDobDate($date)
{
    if ($date && $date != '') {
        $date = explode('/', $date);
        $date = $date[2] . '-' . $date[1] . '-' . $date[0];
        return $date;
    }
}
function backEndDate($date = '')
{
    if ($date && $date != '') {
        return date('Y-m-d', strtotime($date));
    }
    return '';
}
function p($arr = [])
{
    echo "<pre/>";
    print_r($arr);
    exit;
}
function jwtAuthUser()
{
    return JWTAuth::user() ?? '';
}
function getUniqueId()
{
    return uniqid() ?? '';
}
function fileUploadPublic($requestFile, $path = '')
{
    if ($requestFile && $path) {
        $path = str_replace("public/","",$path);
        $newImgName = time() . '.' . $requestFile->getClientOriginalExtension();
        $filePath =  $path . '/' . $newImgName;
        if (Storage::disk('public_uploads')->put($filePath, file_get_contents($requestFile))) {
            return $newImgName ?? '';
        }
        return 0;
    }
    return 0;
}
function fileDeletePublic($fileName = '', $path = '')
{
    $filePath = public_path($path . '/' . $fileName);
    if (file_exists($filePath)) {
        File::delete($filePath);
    }
}
function todayDate()
{
    return date("Y-m-d");
}
function encryptId($id = '')
{
    if ($id && $id != '') {
        // $key = config('constants.acct_enc_key');
        // $method = config('constants.encryption_method');
        // return openssl_encrypt($id, $method, $key);
        
        return Crypt::encrypt($id);
    }
    return 0;
}
function decryptId($id = '')
{
    if ($id && $id != '') {
        // $key = config('constants.acct_enc_key');
        // $method = config('constants.encryption_method');
        // return openssl_decrypt($id, $method, $key);
        return Crypt::decrypt($id);
    }
    return 0;
}
function getTemplateInfo($slug = '', $type = '', $templateVariablesArr = [])
{
    if ($slug != '' && $type != '') {
        $message = '';
        $subject = '';
        $templateExist = TemplateManagement::where('slug', removeSpaces($slug))->where('template_type', $type)->first();
        if (isset($templateExist) && !empty($templateExist)) {
            foreach ($templateVariablesArr as $key => $val) {
                $message = str_replace($key, $val, $templateExist->template);
                $templateExist->template = $message;
            }
            $subject = $templateExist->subject;
            return [
                'message' => $message,
                'subject' => $subject,
            ];
        }
        return 0;
    }
    return 0;
}
function sendEmail($receiver = '', $mailData = [])
{
    if ($receiver != '') {
        $mailSend = Mail::to($receiver)->send(new DefaultMail($mailData));
        if ($mailSend) {
            return 1;
        }
    }
    return 0;
}
function removeSpaces($string = '')
{
    if ($string != '') {
        return str_replace(' ', '', $string);
    }
    return 0;
}
function slugGenerator($string)
{
    if ($string != '') {
        $string = str_replace(' ', '_', $string);
        return preg_replace('/[^A-Za-z0-9\_]/', '', $string);
    }
    return 0;
}
function sendNotification($peram)
{
    Config::get('constants.FCM_SERVER_KEY');
    if (!empty($peram['user_id'])) {
        $firebaseToken = DeviceToken::where('user_id', $peram['user_id'])
            ->whereNotNull('device_token')->pluck('device_token')->all();
        if (!empty($firebaseToken)) {
            $serverApiKey = Config::get('constants.FCM_SERVER_KEY');
            $data = [
                "registration_ids" => $firebaseToken,
                "notification" => [
                    "title" => !empty($peram['title']) ? $peram['title'] : '',
                    "body" => !empty($peram['body']) ? $peram['body'] : '',
                ]
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=' . $serverApiKey,
                'Content-Type: application/json',
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            $response = curl_exec($ch);
            if (json_decode($response)->success == '1') {
                return 1;
            }
            return 0;
        }
        return 0;
    }
    return 0;
}

function formValidation($stepArray, $stepNo)
{
    $validationArray = [];
    $ifExit = false;
    for ($i = 1; $i <= $stepNo; $i++) {
        $ifExit = false;
        foreach ($stepArray as $key => $step) {
            if ($key == 'step' . $i) {
                $ifExit = true;
            }
        }
        if ($ifExit === false) {
            echo $i;
            unset($stepArray['step' . $i]);
        }
    }
    $validationArray = [];
    $fillInput = [];
    foreach ($stepArray as $key => $step) {
        foreach ($step as $stepData) {
            foreach ($stepData as $stepInputKey => $stepInput) {
                array_push($fillInput, $stepInputKey);
                if ($stepInput['required'] == 'Y') {
                    $validationArray[$stepInputKey] = 'required';
                }
                if (!empty($stepInput['otherValidation'])) {
                    foreach ($stepInput['otherValidation'] as $inputValidation) {
                        $validationArray[$stepInputKey] =
                            !empty($validationArray[$stepInputKey])
                            ? $validationArray[$stepInputKey] . '|' . $inputValidation
                            : $inputValidation;
                    }
                }
            }
        }
    }
    return ['validationArray' => $validationArray, 'fillInput' => $fillInput];
}

function checkFileExitOrNot($fileName = '', $path = '', $type = '')
{
    if(env('APP_TYPE') == 'live'){
        $path = str_replace("public/public/","public/",$path);
    }else{
        $path = str_replace("public/","",$path);
        
    }
    
    if (!empty($fileName)) {
        $filePath = $path . '/' . $fileName;
        if (file_exists($filePath)) {
            return true;
        }
    } else {
        return false;
    }
}
function convertStringToLowercase($string)
{
    if (!empty($string)) {
        return strtolower($string);
    } else {
        return '';
    }
}
// function for api response generate
function apiResponse($statusCode, $status, $message, $data)
{
    $response['status_code'] = $statusCode;
    $response['success'] = $status;
    $response['message'] = $message;
    $response['data'] = $data;
    return $response;
}
function getVideoImage($videoFile)
{
    //get filename with extension
    $filenamewithextension = $videoFile->getClientOriginalName();

    //get filename without extension
    $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

    //get file extension
    $extension = $videoFile->getClientOriginalExtension();

    $time = time();

    //filename to store
    $filenametostore = $filename . '_' . $time . '.' . $extension;

    //small thumbnail name
    $smallthumbnail = $filename . '_small_' . $time . '.' . $extension;

    //Upload File
    $videoFile->storeAs('public/profile_images', $filenametostore);
    $videoFile->storeAs('public/profile_images/thumbnail', $smallthumbnail);

    //create small thumbnail
    public_path('storage/profile_images/thumbnail/' . $smallthumbnail);
}
function numberToShort($number)
{
    $units = ['', 'k', 'M', 'B', 'T'];
    $unitIndex = 0;

    while ($number >= 1000) {
        $number /= 1000;
        $unitIndex++;
    }

    return round($number, 1) . $units[$unitIndex];
}

function getFileType($filename)
{
    $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
    $allowedVideoExtensions = ['mp4', 'mov', 'avi', 'mkv'];

    if (in_array($fileExtension, $allowedVideoExtensions)) {
        $type = 'video';
    } else {
        $type = 'image';
    }
    return $type;
}

function getCountries()
{
    return Country::all()->toArray();
}

function changeCountryArray($column, $countryCounts)
{
    $allCountry = getCountries();
    $countryProCounts = [];
    if (!empty($allCountry) && !empty($countryCounts)) {
        foreach ($allCountry as $key => $value) {
            foreach ($countryCounts as $valueCount) {
                if ($value[$column] == $valueCount[0]) {
                    $countryProCounts[$key][0] = ($column == 'code') ? strtolower($value[$column]) : $value[$column];
                    $countryProCounts[$key][1] = $valueCount[1];
                    break;
                } else {
                    $countryProCounts[$key][0] = ($column == 'code') ? strtolower($value[$column]) : $value[$column];
                    $countryProCounts[$key][1] = 0;
                }
            }
        }
    }
    return $countryProCounts;
}
function emptyCountryArray($column)
{
    $allCountry = getCountries();
    $countryProCounts = [];
    if (!empty($allCountry)) {
        foreach ($allCountry as $key => $value) {
            $countryProCounts[$key][0] = ($column == 'code') ? strtolower($value[$column]) : $value[$column];
            $countryProCounts[$key][1] = 0;
        }
    }
    return $countryProCounts;
}

function convertToPercentage($countArray, $total)
{
    if (!empty($countArray)) {
        foreach ($countArray as $key => $val) {
            $countArray[$key][1] = round(($val[1] * 100 / $total), 2);
        }
    }
    return $countArray;
}
// This function is use for get project type
function getProjectType($projectType)
{
    if ($projectType == '1') {
        $projectTypeName = 'New';
    } elseif ($projectType == '2') {
        $projectTypeName = 'Existing';
    } elseif ($projectType == '3') {
        $projectTypeName = 'Emergency Relief';
    } else {
        $projectTypeName = '';
    }
    return $projectTypeName;
}

// This function is use for get project donation type
function getProjectDonationType($projectDonationType)
{
    $projectDonationTypeIds = explode(",", $projectDonationType);
    $projectDonationTypeValue = "";
    foreach ($projectDonationTypeIds as $projectType) {
        if ($projectType == 1) {
            $projectDonationTypeValue .= 'Donation,';
        }
        if ($projectType == 2) {
            $projectDonationTypeValue .= 'Volunteer,';
        }
    }
    return rtrim($projectDonationTypeValue, ',');
}

// This function is use for get project sdg value
function getSdg($sdgIds)
{
    if (!empty($sdgIds)) {
        $explodSdgId = explode(",", $sdgIds);
        $sdgNames = Sdg::whereIn('id', $explodSdgId)->get()->pluck('name')->toArray();
        $sdgValue = implode(",", $sdgNames);
    } else {
        $sdgValue = '-';
    }
    return $sdgValue;
}

function getProductGallery($project){
    $gallery=[];
    if(!empty($project->default_image)){
        $defaultImg['type']='image';
        $defaultImg['url']=asset(config('constants.user_project_path') . '/' . $project->default_image);
        $gallery[]=$defaultImg;
    }
    if ($project->hasProjectDocuments->isNotEmpty()) {
        foreach ($project->hasProjectDocuments as $document) {
            if ($document && $document->document_type == 'image') {
                $defaultImg['type']='image';
                $defaultImg['url']=asset(config('constants.user_project_path') . '/' . $document->document_name);
                $gallery[]=$defaultImg;
            } 
            elseif ($document && $document->document_type == 'video') {
                $defaultImg['type']='video';
                $defaultImg['url']=asset(config('constants.user_project_path') . '/' . $document->document_name);
                $gallery[]=$defaultImg;
            }
        }
    }
    return $gallery;
}