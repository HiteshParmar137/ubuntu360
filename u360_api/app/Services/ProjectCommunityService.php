<?php

namespace App\Services;

use App\Models\ProjectCommunity;
use App\Models\ProjectCommunityLike;
use App\Models\ProjectDonation;
use App\Models\ProjectFollow;
use App\Models\ProjectVolunteer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ProjectCommunityService
{
    protected $currentPath;
    public function __construct()
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }

    //create function for add community post data
    public function addPost($postData)
    {
        try {
            $community = new ProjectCommunity;
            $community->project_id = $postData['project_id'];
            $user = jwtAuthUser();
            $community->user_id = $user->id;
            $community->comment = $postData['comment'];
            $postDataArray['document'] = '';
            if (!empty($postData['document'])) {
                $document = fileUploadPublic($postData['document'], config('constants.community_file_upload_path'));
                $community->document = $document;
                $postDataArray['document'] = asset(config('constants.community_file_upload_path') . '/' . $document);
            }
            $community->save();
            $postDataArray['user_name'] = $user->name;

            $postDataArray['comment'] = $postData['comment'];
            $projectCommuniteArray['sr_no'] = 1;
            $projectCommuniteArray['project_id'] = encryptId($community->project_id);
            $projectCommuniteArray['project_community_id'] = encryptId($community->id);
            $projectCommuniteArray['comment'] = $community->comment;
            $projectCommuniteArray['type'] = $community->type;
            $projectCommuniteArray['user']['name'] = $user->name;
            if (!empty($user->image)) {
                $projectCommuniteArray['user']['image'] = asset(
                    config('constants.user_profile_img_path') . '/' . $user->image ?? ''
                );
            } else {
                $projectCommuniteArray['user']['image'] = asset(config('constants.no_user_image_path'));
            }
            $projectCommuniteArray['is_like'] = '0';

            $projectCommuniteArray['like_count'] = 0;
            $projectCommuniteArray['comment_count'] = 0;
            $projectCommuniteArray['comments'] = [];
            $projectCommuniteArray['likes'] = [];
            if (!empty($community->document)) {
                $documentPath = config('constants.community_file_upload_path');
                $projectCommuniteArray['document_type'] = getFileType($community->document);
                $projectCommuniteArray['document'] = asset($documentPath . '/' . $community->document);
            } else {
                $projectCommuniteArray['document'] = '';
            }
            $projectCommuniteArray['type'] = 0;
            $response['status'] = true;
            $response['post_data'] = $projectCommuniteArray;
            $response['message'] = config('message.front_user.community_post_add_successfuly');
            return $response;
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $postData,
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return $response;
        }
    }

    //create common function for get community list
    public function getCommunityList($postData)
    {
        $user = jwtAuthUser();
        if (isset($postData['project_id']) && !empty($postData['project_id'])) {
            $projectCommunites = ProjectCommunity::where('project_id', $postData['project_id'])
                ->with(
                    'hasUser',
                    'hasLikes',
                    'hasComments',
                    'hasComments.hasUser',
                    'hasProject',
                    'hasDonation',
                    'hasVolunteer'
                );
        } elseif (isset($postData['project_id_arr']) && !empty($postData['project_id_arr'])) {
            $projectCommunites = ProjectCommunity::whereIn('project_id', $postData['project_id_arr'])
                ->with(
                    'hasUser',
                    'hasLikes',
                    'hasComments',
                    'hasComments.hasUser',
                    'hasProject',
                    'hasDonation',
                    'hasVolunteer'
                );
        } else {
            $projectCommunites = ProjectCommunity::with(
                'hasUser',
                'hasLikes',
                'hasComments',
                'hasComments.hasUser',
                'hasProject',
            );
        }

        $projectCommunites = $projectCommunites->orderBy('created_at', 'desc')->paginate(env('PAGINATION_COUNT'));
        $response = [];
        $page = $postData['page'];
        $projectCommuniteArray = [];
        if ($projectCommunites->count() > 0) {
            foreach ($projectCommunites as $k => $projectCommunite) {
                $projectCommuniteArray['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                $projectCommuniteArray['project_id'] = encryptId($projectCommunite['project_id']);
                $projectCommuniteArray['project_title'] = $projectCommunite->hasProject->title;
                $projectCommuniteArray['project_community_id'] = encryptId($projectCommunite['id']);
                $projectCommuniteArray['type'] = $projectCommunite['type'];
                if ($projectCommunite['type'] == '1') {
                    $projectCommuniteArray['comment'] = $projectCommunite->hasDonation->comment ?? '';
                    $amount = (!empty($projectCommunite->hasDonation->donation_amount))
                        ? number_format($projectCommunite->hasDonation->donation_amount, 2, '.', ',') : 0;
                    $projectCommuniteArray['donation_amount'] = 'Donated $' . $amount;
                    if (!empty($projectCommunite->hasDonation->document)) {
                        $donationDocumentPath = config('constants.donation_file_upload_path');
                        $projectCommuniteArray['document_type'] = getFileType($projectCommunite->document);
                        $donationDocumentName = $projectCommunite->hasDonation->document;
                        $projectCommuniteArray['document'] = asset($donationDocumentPath . '/' . $donationDocumentName);
                    } else {
                        $projectCommuniteArray['document'] = '';
                    }
                } elseif ($projectCommunite['type'] == '2') {
                    $projectCommuniteArray['volunteer_title'] = 'Applied for Volunteering';
                    $projectCommuniteArray['comment'] = $projectCommunite->hasVolunteer->comment ?? '';
                    $projectCommuniteArray['document'] = '';
                } else {
                    $projectCommuniteArray['comment'] = $projectCommunite['comment'];
                    if (!empty($projectCommunite['document'])) {
                        $projectCommuniteArray['document_type'] = getFileType($projectCommunite->document);
                        $documentPath = config('constants.community_file_upload_path');
                        $projectCommuniteArray['document'] = asset($documentPath . '/' . $projectCommunite->document);
                    } else {
                        $projectCommuniteArray['document'] = '';
                    }
                }

                $projectCommuniteArray['user']['name'] = $projectCommunite->hasUser->name;
                $projectCommuniteArray['user']['image'] = (!empty($projectCommunite->hasUser->image))
                    ? asset(config('constants.user_profile_img_path') . '/' . $projectCommunite->hasUser->image)
                    : '';
                $projectCommuniteArray['is_like'] = '0';
                if (!empty($user)) {
                    $userId = $user->id;
                    $condition = ['user_id' => $userId, 'project_community_id' => $projectCommunite['id']];
                    $isLikeCount = ProjectCommunityLike::where($condition)->count();
                    if ($isLikeCount > 0) {
                        $projectCommuniteArray['is_like'] = '1';
                    }
                }
                $projectCommuniteArray['like_count'] = numberToShort($projectCommunite->hasLikes->count());
                $projectCommuniteArray['comment_count'] = numberToShort($projectCommunite->hasComments->count());
                $projectCommuniteArray['comments'] = [];
                if ($projectCommunite->hasComments->count() > 0) {
                    foreach ($projectCommunite->hasComments as $comment) {
                        $commentArray['comment'] = $comment->comment;
                        $commentArray['name'] = $comment->hasUser->name;
                        if (!empty($comment->hasUser->image)) {
                            $commentUserImage = $comment->hasUser->image;
                            $commentUserImagePath = config('constants.user_profile_img_path');
                            $commentArray['profile_image'] = asset($commentUserImagePath . '/' . $commentUserImage);
                        } else {
                            $commentArray['profile_image'] = asset(config('constants.no_user_image_path'));
                        }
                        $projectCommuniteArray['comments'][] = $commentArray;
                    }
                }
                $projectCommuniteArray['likes'] = [];
                if ($projectCommunite->hasComments->count() > 0) {
                    foreach ($projectCommunite->hasLikes as $like) {
                        $likeArray['name'] = $like->hasUser->name;
                        $likeUserImage = $like->hasUser->image;
                        $likeUserImagePath = config('constants.user_profile_img_path');
                        $likeArray['profile_image'] = asset($likeUserImagePath . '/' . $likeUserImage);
                        $projectCommuniteArray['likes'][] = $likeArray;
                    }
                }

                $response[] = $projectCommuniteArray;
            }
        }
        if (!empty($postData['project_id'])) {
            $topDonation = ProjectDonation::where('project_id', $postData['project_id'])
                ->with('hasUser', 'hasProject')
                ->orderByDesc('donation_amount')
                ->limit(config('constants.to_donation_limit'))->get();
        } else {
            $topDonation = ProjectDonation::with('hasUser', 'hasProject')
                ->orderByDesc('donation_amount')
                ->limit(config('constants.to_donation_limit'))->get();
        }

        $topDoantionList = [];
        if ($topDonation->count() > 0) {
            foreach ($topDonation as $donation) {
                $topDonationArray['name'] = $donation->hasUser->name ?? '';
                $topDonationArray['project_title'] = $donation->hasProject->title ?? '';
                $topDonationArray['donation_amount'] = $donation->donation_amount ?? 0;
                $topDonationArray['profile_image'] = (!empty($donation->hasUser->image))
                    ? asset(config('constants.user_profile_img_path') . '/' . $donation->hasUser->image)
                    : asset(config('constants.no_user_image_path'));

                $topDoantionList[] = $topDonationArray;
            }
        }
        $paginateData = $projectCommunites->toArray();
        $pagination =  pagination($paginateData);
        $userDetails['total_volunteer'] = 0;
        $userDetails['total_donation'] = 0;
        $userDetails['total_causes'] = '$0.00';
        if (!empty($user)) {
            $userDetails['total_volunteer'] = ProjectVolunteer::where('user_id', $user->id)->count();
            $query = ProjectDonation::select(DB::raw('SUM(donation_amount) as amount'))
            ->where('user_id', $user->id)->first();
            $userDetails['total_donation'] = $query->amount ?? 0;
            // p($userDetails['total_donation']);
            $userDetails['total_causes'] = ProjectFollow::where('user_id', $user->id)->count();
        }
        $dataArr = [
            'response' => $response,
            'top_donation' => $topDoantionList,
            'pagination' => $pagination,
            'user_details' => $userDetails,
            'status' => true,
            'message' => config('message.front_user.no_data_found'),
        ];
        if ($projectCommunites->count() == 0) {
            $dataArr['success'] = false;
            $dataArr['message'] = config('message.front_user.no_data_found');
        }
        return $dataArr;
    }
}
