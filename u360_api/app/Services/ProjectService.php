<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\ProjectDocument;
use App\Models\ProjectDonation;
use App\Models\ProjectFollow;
use App\Models\ProjectReview;
use App\Models\ProjectVolunteer;
use App\Models\Sdg;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectService
{
    public function index($projectArr = [])
    {
        $dataArr = [];
        $projects = Project::query();
        //particular user's data
        $user = jwtAuthUser();
        if (isset($projectArr['user']) && $projectArr['user'] == '1') {
            $userId = $user->id;
            $projects = $projects->where('user_id', $userId ?? 0);
            
        }
        if(!empty($user->id)){
            $userId = $user->id;
            $projects = $projects->with('hasFollows', function ($projectFollow) use ($userId) {
                $projectFollow->where('user_id', $userId);
            });
        }
        
        //search by title
        (isset($projectArr['srchByTitle']) && $projectArr['srchByTitle'] != '') ?
            $projects = $projects->Where('title', 'LIKE', '%' . $projectArr['srchByTitle'] . '%')
            : '';

        (isset($projectArr['srchByCategory']) && $projectArr['srchByCategory'] != '') ?
        $projects = $projects->Where('category_id', $projectArr['srchByCategory'])
            : '';

        //search by user
        (isset($projectArr['srchByUser']) && $projectArr['srchByUser'] != '') ?
            $projects =  $projects->Where('user_id', $projectArr['srchByUser'])
            : '';

        //search by status
        if (isset($projectArr['status']) && count($projectArr['status'])) {
            $projects =  $projects->WhereIn('status', $projectArr['status']);
        } elseif (isset($projectArr['srchByStatus']) && $projectArr['srchByStatus'] != '') {
            $projects =  $projects->Where('status', $projectArr['srchByStatus']);
        } else {
            $isAdmin = false;
            if (!empty($user->user_group_id)) {
                $isAdmin = true;
            }
            if (!$isAdmin) {
                $projects =  $projects->Where('status', 'Approved');
            }
        }

        //particular projectIds data
        if (isset($projectArr['projectIds'])) {
            $projects =  $projects->WhereIn('id', $projectArr['projectIds']);
        }

        $projects = $projects->withCount([
            'hasDonations AS totalAmount' => function ($transaction) {
                $transaction->where('status', config('constants.paid_status'))->select(DB::raw("SUM(donation_amount)"));
            }
        ]);
        $projects = $projects->withCount([
            'hasVolunteer AS totalVolunteer' => function ($transaction) {
                $transaction->select(DB::raw("COUNT(*)"));
            }
        ]);
        $projects = $projects->with(
            'hasProjectCategory',
            'hasUser',
            'hasProjectDocuments'
        )->orderBy('created_at', 'desc');
        $projects = $projects->paginate(env('PAGINATION_COUNT'));
        $response = [];
        $page = $projectArr['page'];
        if (isset($projects) && !empty($projects)) {
            foreach ($projects as $k => $project) {
                $projArr = [];
                $projImages = $projVids = $projDocs = $proSponsors = [];
                if ($project->hasProjectDocuments->isNotEmpty()) {
                    foreach ($project->hasProjectDocuments as $document) {
                        if ($document && $document->document_type == 'image') {
                            $projImages[] = asset(
                                config('constants.user_project_path') . '/' . $document->document_name
                            );
                        } elseif ($document && $document->document_type == 'video') {
                            $projVids[] = asset(config('constants.user_project_path') . '/' . $document->document_name);
                        } else {
                            $projDocs[] = asset(config('constants.user_project_path') . '/' . $document->document_name);
                        }
                    }
                }
                $percentageAmount = 0;
                if ($project->amount && $project->amount > 0) {
                    $percentageAmount = $project->totalAmount * 100 / $project->amount;
                }
                $donation = ProjectDonation::query();
                $donation = $donation->where('project_id', $project->id);
                $donation = $donation->orderBy('created_at', 'DESC');
                $donation = $donation->first();
                $projArr['last_donation'] = !empty($donation->created_at) ?
                    Carbon::parse($donation->created_at)->diffForHumans()
                    :  '';

                $projArr['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                $projArr['id'] = encryptId($project->id) ?? 0;
                $projArr['title'] = $project->title ?? '';
                $projArr['likeCount'] = $project->total_likes ?? 0;
                $projArr['followCount'] = $project->total_follows ?? 0;
                $projArr['commentCount'] = $project->total_comments ?? 0;
                $projArr['status'] = $project->status ?? '';
                $projArr['reject_reason'] = $project->reject_reason ?? '';
                $projArr['user'] = $project->hasUser->name ?? '';
                $projArr['userId'] = $project->user_id ?? '';
                $projArr['description'] = $project->description ?? '';
                $projArr['project_amount'] = $project->amount ?? 0;
                $projArr['total_amount'] = $project->totalAmount ?? 0;
                $projArr['totalVolunteer'] = $project->totalVolunteer ?? 0;
                $projArr['volunteer'] = $project->volunteer ?? 0;
                $leftAmount = $projArr['project_amount'] - $projArr['total_amount'];
                $leftAmount = $leftAmount>0 ? $leftAmount : 0.00;
                $projArr['left_amount'] = number_format((float)$leftAmount, 2, '.', '');
                $projArr['percentage_amount'] = (int)$percentageAmount;
                $projectDonationTypeArray = [];
                if (!empty($project->project_donation_type)) {
                    $projectDonationType = explode(',', $project->project_donation_type);
                    foreach ($projectDonationType as $donationType) {
                        $projectDonationTypeArray[] = (int)$donationType;
                    }
                }
                $projArr['project_donation_type'] = $projectDonationTypeArray;
                if ($projArr['status'] != 'Draft') {
                    $projArr['status'] = ($this->checkIsGoalReached($projArr) != '') ?
                        $this->checkIsGoalReached($projArr) :
                        $projArr['status'];
                }

                $defaultImg = checkFileExitOrNot(
                    $project->default_image,
                    public_path(config('constants.user_project_path'))
                );
                $projArr['default_image'] = $defaultImg === true
                    ?  asset(config('constants.user_project_path') . '/' . $project->default_image)
                    : asset(config('constants.no_image_path'));

                $sdgs = [];
                if (!empty($project->sdg_ids)) {
                    $sdgIds = explode(',', $project->sdg_ids);
                    $sdgsList = Sdg::select('id', 'name', 'icon')->whereIn('id', $sdgIds)->get();
                    if (!empty($sdgsList)) {
                        $sdgs = $sdgsList->toArray();
                    }
                }
                $projArr['review'] = '';
                $projArr['starsHTML'] = '';
                $projArr['reviewCount'] = 0;
                if (!empty($project->id)) {
                    $proReview = DB::table('project_review')->where('project_id', $project->id)->avg('rating');
                    $reviewCount = DB::table('project_review')->where('project_id', $project->id)->count('*');

                    $projArr['review'] = number_format((float)$proReview, 2, '.', '');
                    $projArr['reviewCount'] = $reviewCount;
                    $whole = floor($projArr['review']);      // 1
                    $fraction = $projArr['review'] - $whole; // .25
                    $projArr['decimalReview'] = number_format((float)$fraction, 2, '.', '');
                    $projArr['starsHTML'] = $this->getRatingStartsHTML($projArr['review']);
                }
                $projArr['sdgs'] = $sdgs;
                $projArr['category_name'] = $project->hasProjectCategory->name ?? '';
                $projArr['category_id'] = $project->hasProjectCategory->id ?? '';
                $projArr['isFollow'] = $project->hasFollows->count() && $project->hasFollows->count() > 0 ? '1' : '0';
                $projArr['project_images'] = $projImages;
                $projArr['project_videos'] = $projVids;
                $projArr['project_documents'] = $projDocs;
                if ($project->hasDonations->unique('user_id')->isNotEmpty()) {
                    foreach ($project->hasDonations->unique('user_id') as $sponsor) {
                        $sponsorsArr = [];
                        $sponsorsArr['name'] = $sponsor->hasUser->name ?? '';
                        $sponsorsArr['image'] = (isset($sponsor->hasUser->image) && $sponsor->hasUser->image != '') ?
                            asset(config('constants.user_profile_img_path') . '/' . $sponsor->hasUser->image ?? '')
                            : asset(config('constants.no_user_image_path'));
                        $proSponsors[] = $sponsorsArr;
                    }
                }
                $projArr['sponsors'] = $proSponsors;
                $response[] = $projArr;
            }
        }
        $paginateData = $projects->toArray();
        $pagination =  pagination($paginateData);
        $dataArr = [
            'response' => $response,
            'pagination' => $pagination,
            'success' => true
        ];
        if ($projects->count() == 0) {
            $dataArr['success'] = false;
        }
        return $dataArr;
    }

    public function details($projectArr = [])
    {
        $response = $categories = [];
        $dataArr = [
            'response' => $response,
            'success' => true,
            'message' => '',
        ];
        $projectId = $projectArr['id'];
        $projectId = decryptId($projectId);
        $project = Project::query();
        if (isset($projectArr['user_id']) && !empty($projectArr['user_id'])) {
            $userId = $projectArr['user_id'];
            $project = $project->where('user_id', $projectArr['user_id']);
            $project = $project->with('hasFollows', function ($projectFollow) use ($userId) {
                $projectFollow->where('user_id', $userId);
            });
        }
        $project = $project->where('id', $projectId);
        $project = $project->with(
            'hasProjectDocuments',
            'hasProjectCategories',
            'hasUser',
            'hasUser.hasUserDetails',
            'hasRejecReason',
            'hasVolunteer'
        );
        $project = $project->withCount([
            'hasDonations AS totalAmount' => function ($transaction) {
                $transaction->where('status', config('constants.paid_status'))->select(DB::raw("SUM(donation_amount)"));
            }
        ]);
        $project = $project->withCount([
            'hasVolunteer AS totalVolunteer' => function ($transaction) {
                $transaction->select(DB::raw("COUNT(*)"));
            }
        ]);
        $project = $project->first();
        if (empty($project) && !isset($project)) {
            $dataArr['success'] = false;
            $dataArr['message'] = config('message.front_user.project_not_found');
            return $dataArr;
        }
        if (!empty($project->project_donation_type)) {
            $projectDonationType = explode(',', $project->project_donation_type);
            foreach ($projectDonationType as $donationType) {
                $projectDonationTypeArray[] = (int)$donationType;
            }
        }
        $sdgs = [];
        $sdgNameString = '';
        if (!empty($project->sdg_ids)) {
            $projectSdgIds = explode(',', $project->sdg_ids);
            foreach ($projectSdgIds as $sdgId) {
                $projectSdgIdsArray[] = (int)$sdgId;
            }
            $sdgsList = Sdg::select('id', 'name', 'icon')->whereIn('id', $projectSdgIds)->get();
            if (!empty($sdgsList)) {
                $sdgs = $sdgsList->toArray();
                foreach ($sdgs as $sdgName) {
                    $sdgNameString .= $sdgName['name'] . ', ';
                }
                $sdgNameString = rtrim($sdgNameString, ', ');
            }
        }
        $response['review'] = '';
        $response['starsHTML'] = '';
        $response['reviewCount'] = 0;
        $response['all_reviews'] = [];
        if (!empty($project->id)) {
            $proReview = DB::table('project_review')->where('project_id', $project->id)->avg('rating');
            $reviewCount = DB::table('project_review')->where('project_id', $project->id)->count('*');
            $response['review'] = number_format((float)$proReview, 2, '.', '');
            $response['reviewCount'] = $reviewCount;
            $whole = floor($response['review']);      // 1
            $fraction = $response['review'] - $whole; // .25
            $response['decimalReview'] = number_format((float)$fraction, 2, '.', '');
            $response['starsHTML'] = $this->getRatingStartsHTML($response['review']);

            $proAllReview = DB::table('project_review')->where('project_id', $project->id)->get();
            if (!empty($proAllReview)) {
                $allReview = $proAllReview->toArray();
                $i = 0;
                foreach ($allReview as $reviews) {
                    $response['all_reviews'][$i]['rating'] = $reviews->rating;
                    $response['all_reviews'][$i]['starsHTML'] = $this->getRatingStartsHTML($reviews->rating);
                    $response['all_reviews'][$i]['user_id'] = $reviews->user_id;
                    $user = User::find($reviews->user_id);
                    $response['all_reviews'][$i]['user_name'] = $user->name;
                    $response['all_reviews'][$i]['profile_image'] = (isset($user->image) && $user->image != '') ?
                    asset(config('constants.user_profile_img_path') . '/' . $user->image)
                    : asset(config('constants.no_user_image_path'));
                    $response['all_reviews'][$i]['comment'] = $reviews->comment;
                    $response['all_reviews'][$i]['created_at'] = $reviews->created_at;
                    $i++;
                }
            }
        }
        $response['donations'] = [];
        $response['last_donation'] = '';
        if (!empty($project->id)) {
            $donation = ProjectDonation::query();
            $donation = $donation->where('project_id', $project->id);
            $donation = $donation->orderBy('created_at', 'DESC');
            $donation = $donation->limit('5');
            $donation = $donation->get();
            $i = 0;
            if (!empty($donation)) {
                if (!empty($donation[0]->created_at)) {
                    $response['last_donation'] = Carbon::parse($donation[0]->created_at)->diffForHumans();
                }
                foreach ($donation as $item) {
                    $response['donations'][$i]['user_id'] = $item->user_id;
                    $user = User::find($item->user_id);
                    $response['donations'][$i]['user_name'] = !empty($user->name) ? $user->name : '';
                    $response['donations'][$i]['profile_image'] = (isset($user->image) && $user->image != '') ?
                        $user->image : 'user.png';
                    $response['donations'][$i]['profile_image'] =
                        (isset($user->image) && $user->image != '') ?
                        asset(config('constants.user_profile_img_path') . '/' . $user->image) :
                        asset(config('constants.no_user_image_path'));
                    $response['donations'][$i]['comment'] = $item->comment;
                    $response['donations'][$i]['amount'] = $item->donation_amount;
                    $response['donations'][$i]['created_at'] = $item->created_at;
                    $i++;
                }
            }
        }

        $projectUser = User::find($project->user_id);
        $response['project_user'] = '';
        $response['project_user_type'] = '';
        $response['project_user_image'] = '';
        if (!empty($projectUser)) {
            $response['project_user'] = $projectUser->name;
            $response['project_user_type'] = $projectUser->user_type;
            $response['project_user_image'] =
                (isset($projectUser->image) && $projectUser->image != '') ?
                asset(config('constants.user_profile_img_path') . '/' . $projectUser->image) :
                asset(config('constants.no_user_image_path'));
        }
        $percentageAmount = 0;
        if ($project->amount && $project->amount > 0) {
            $percentageAmount = $project->totalAmount * 100 / $project->amount;
        }
        $response['p_id'] = encryptId($project->id);
        $response['project_id'] = $project->id;
        $projArr['sdgs'] = $sdgs;
        $response['title'] = $project->title ?? '';
        $response['project_donation_type'] =  $projectDonationTypeArray ?? '';
        $response['sdg_ids'] =  $projectSdgIdsArray ?? '';
        $response['sdgs'] =  $sdgs ?? '';
        $response['sdgName'] = $sdgNameString;
        $response['category_id'] = $project->category_id ?? '';
        $response['category_name'] = $project->hasProjectCategory->name ?? '';
        $response['contact_number'] = $project->hasUser->hasUserDetails->contact_number ?? '';
        $response['email'] = $project->hasUser->email ?? '';
        $response['description'] = $project->description ?? '';
        $response['project_url'] = $project->project_url ?? '';
        $response['amount'] = $project->amount ?? 0;
        $response['volunteer'] = $project->volunteer ?? 0;
        $leftVolunteer= $project->volunteer - $project->totalVolunteer;
        $response['totalVolunteer'] = $project->totalVolunteer ?? 0;
        $response['leftVolunteer'] = ($leftVolunteer > 0) ? $leftVolunteer : 0;
        $response['totalAmount'] = $project->totalAmount ?? 0;
        $leftAmount = $response['amount'] - $response['totalAmount'];
        $leftAmount = $leftAmount>0 ? $leftAmount : 0.00;
        $response['left_amount'] = number_format((float)$leftAmount, 2, '.', '');
        $response['percentage_amount'] = (int)$percentageAmount;
        $defaultImg = checkFileExitOrNot($project->default_image, public_path(config('constants.user_project_path')));
        $response['default_image'] = $defaultImg == -true
            ? asset(config('constants.user_project_path') . '/' . $project->default_image)
            : asset(config('constants.no_image_path'));
        $response['default_image_name'] = $project->default_image ?? '';
        $response['status'] = $project->status ?? '';
        $response['gallery'] = getProductGallery($project);
        if ($response['status'] != 'Draft') {
            $response['project_amount'] = $project->amount ?? 0;
            $response['total_amount'] = $project->totalAmount ?? 0;
            $response['status'] = ($this->checkIsGoalReached($response) != '') ?
                $this->checkIsGoalReached($response) :
                $response['status'];
            unset($response['project_amount']);
            unset($response['total_amount']);
        }
        $response['sdg_id'] = $project->sdg_id ?? '';
        $response['country'] = $project->country ?? '';
        $response['city'] = $project->city ?? '';
        $response['user_id'] = $project->user_id ?? '';
        $response['project_type'] = $project->project_type ?? '';
        $response['isFollow'] = $project->hasFollows->count() && $project->hasFollows->count() > 0 ? '1' : '0';
        $response['project_images'] = [];
        if ($project->hasProjectDocuments->isNotEmpty()) {
            foreach ($project->hasProjectDocuments as $document) {
                $projImages = array();
                $projVids = array();
                $projDocs = array();
                if ($document && $document->document_type == 'image') {
                    $projImages['url'] = asset(config('constants.user_project_path') . '/' . $document->document_name);
                    $response['project_images'][] = $projImages;
                } elseif ($document && $document->document_type == 'video') {
                    $defaultVidImg = asset(config('constants.user_project_path') . '/' . 'video_img.png');
                    $projVids['url'] = asset(config('constants.user_project_path') . '/' . $document->document_name);
                    $projVids['image'] = $defaultVidImg;
                    $response['project_videos'][] = $projVids;
                } else {
                    $ext = pathinfo($document->document_name, PATHINFO_EXTENSION);
                    $docImg = asset(config('constants.user_project_path') . '/' . 'document_img.jpg');
                    if ($ext && $ext == 'pdf') {
                        $docImg = asset(config('constants.user_project_path') . '/' . 'pdf_img.png');
                    } elseif ($ext && $ext == 'xlsx') {
                        $docImg = asset(config('constants.user_project_path') . '/' . 'xls_img.png');
                    }
                    $projDocs['url'] = asset(config('constants.user_project_path') . '/' . $document->document_name);
                    $projDocs['image'] = $docImg;
                    $projDocs['document_name'] = $document->document_original_name;
                    $projDocs['date'] = changeDateFormat($document->created_at, 'F Y');
                    $response['project_documents'][] = $projDocs;
                }
            }
        }
        if ($project->hasProjectCategories->isNotEmpty()) {
            foreach ($project->hasProjectCategories as $projectCategory) {
                $categories[] = $projectCategory;
            }
        }
        if ($project->hasRejecReason->isNotEmpty()) {
            foreach ($project->hasRejecReason as $rejectReason) {
                $response['reject_reason'][]['comment'] = $rejectReason['comment'];
            }
        }
        if ($project->hasVolunteer->isNotEmpty()) {
            $volunteerDetails=[];
            foreach ($project->hasVolunteer as $volunteer) {
                $volunteerDetails['user_name'] = $volunteer['user_name'];
                $volunteerDetails['email'] = $volunteer['email'];
                $volunteerDetails['comment'] = $volunteer['comment'];
                $volunteerDetails['sponsor_type'] = $volunteer['sponsor_type'];
                $volunteerDetails['apply_date'] = $volunteer['apply_date'];
                $response['volunteer_list'][] = $volunteerDetails;
            }
        }

        $response['category_ids'] = $categories;
        $response['user'] = $project->hasUser->name ?? '';

        $dataArr['response'] = $response;
        $dataArr['message'] = config('message.front_user.project_detail_fetch_success');
        return $dataArr;
    }

    public function userProjectsCount($projectArr = [])
    {
        $user = jwtAuthUser();
        $userId = $user->id;
        $projectCount = Project::where('user_id', $userId);
        if (isset($projectArr['status']) && $projectArr['status'] != '') {
            $projectCount = $projectCount->WhereIn('status', $projectArr['status']);
        }
        return $projectCount = $projectCount->count();
    }


    public function feelGoodData($projectArr = [])
    {
        $dataArr = [];
        $projects = Project::query();
        $projects = $projects->withCount([
            'hasTransactions AS totalAmount' => function ($transaction) {
                $transaction->where('type', 'Donation')->select(DB::raw("SUM(amount)"));
            }
        ]);
        if (isset($projectArr['projectIds'])) {
            $projects =  $projects->WhereIn('id', $projectArr['projectIds']);
        }

        $projects = $projects->with('hasDonations.hasUser');
        $projects = $projects->paginate(env('PAGINATION_COUNT'));
        $responseArr = [];
        $page = $projectArr['page'];
        if (isset($projects) && !empty($projects)) {
            foreach ($projects as $k => $project) {
                $response = [];
                $response['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                $response['id'] = encryptId($project->id) ?? 0;
                $response['title'] = $project->title ?? '';
                $response['likeCount'] = $project->total_likes ?? 0;
                $response['followCount'] = $project->total_follows ?? 0;
                $response['commentCount'] = $project->total_comments ?? 0;
                $response['description'] = $project->description ?? '';
                $response['total_amount'] = $project->totalAmount ?? 0;
                $defaultImg = checkFileExitOrNot(
                    $project->default_image,
                    public_path(config('constants.user_project_path'))
                );
                $response['default_image'] = $defaultImg === true
                    ?  asset(config('constants.user_project_path') . '/' . $project->default_image)
                    : asset(config('constants.no_image_path'));
                $sponsorsArr = [];
                if ($project->hasDonations->isNotEmpty()) {
                    foreach ($project->hasDonations as $sponsor) {
                        $sponsorArr = [];
                        $sponsorArr['name'] = $sponsor->hasUser->name ?? '';
                        $sponsorArr['image'] = asset(config(
                            'constants.user_profile_img_path'
                        ) . '/' . $sponsor->hasUser->image
                            ?? asset(config('constants.no_user_image_path')));
                        $sponsorArr['donation'] = $sponsor->total_donation ?? '';
                        $sponsorsArr[] = $sponsorArr;
                    }
                }
                $response['user_details'] = $sponsorsArr;
                $responseArr[] = $response;
            }
        }
        $paginateData = $projects->toArray();
        $pagination =  pagination($paginateData);
        $dataArr = [
            'response' => $responseArr,
            'pagination' => $pagination,
            'success' => true
        ];
        if ($projects->count() == 0) {
            $dataArr['success'] = false;
        }
        return $dataArr;
    }

    public function getProjectDocs($paramsArr = [])
    {
        $response = [];
        $dataArr = [
            'response' => $response,
            'success' => true,
            'message' => '',
        ];
        $id = decryptId($paramsArr['id']);
        $docType = $paramsArr['type'];
        $project = Project::Query();
        $project = $project->where('id', $id);
        $project = $project->with('hasProjectDocuments', function ($projectDocument) use ($docType) {
            $projectDocument->where('document_type', $docType);
        });
        $project = $project->first();
        if ($project->hasProjectDocuments->count() == '0' && $project->hasProjectDocuments->isEmpty()) {
            $dataArr['success'] = false;
            $dataArr['message'] = config('message.front_user.project_docs_not_found');
            return $dataArr;
        }
        if ($project->hasProjectDocuments->isNotEmpty()) {
            foreach ($project->hasProjectDocuments as $document) {
                $projectDocArr = [];
                $projectDocArr['id'] = encryptId($document->id) ?? 0;
                $projectDocArr['document'] = asset(
                    config('constants.user_project_path') . '/' . $document->document_name ?? ''
                );
                $projectDocArr['document_name'] = $document->document_original_name ?? '';
                $response[] = $projectDocArr;
            }
        }
        $dataArr['success'] = true;
        $dataArr['message'] = config('message.front_user.project_docs_fetch_success');
        $dataArr['response'] = $response;
        return $dataArr;
    }

    public function saveProjectDetails($postData = [])
    {
        $response = [];
        $dataArr = [
            'response' => $response,
            'success' => true,
            'message' => '',
        ];
        $project = Project::updateOrCreate([
            'id' => !empty($postData['id']) ? decryptId($postData['id']) : 0,
        ], $postData);

        $message = !empty($postData['id']) ? 'project_updated_success' : 'project_added_success';
        if ($postData['stepType'] && $postData['stepType'] == 'submit') {
            $message = '';
            $user = jwtAuthUser();
            $templateName =  ($project->status == 'Draft') ?
                'new_project_create' : 'project_updated';
            $mailBody = getTemplateInfo($templateName, 'email', [
                '##NAME##' => !empty($user->name) ? $user->name : '',
                '##TITLE##' => !empty($user->name) ? $project->title : '',
            ]);
            $mailData = [
                'message' => $mailBody['message'] ?? '',
                'subject' => $mailBody['subject'] ?? '',
            ];
            $dataArr['message'] = '';
            $dataArr['response'] = $mailData;
            sendEmail(config('constants.admin_mail'), $mailData);
            $project->update(['status' => 'Pending']);
            $message = 'project_submit_success';
        }
        $dataArr['message'] = config('message.front_user.' . $message . '');
        $dataArr['response'] = encryptId($project->id);
        return $dataArr;
    }

    public function saveProjectDocuments($postData = [])
    {
        $response = $pojectDocumentArr = [];
        $dataArr = [
            'response' => $response,
            'success' => true,
            'message' => '',
        ];
        $id = decryptId($postData['dataArr']['project_id']);
        $project = Project::where('id', $id);
        if (isset($postData['userId']) && !empty($postData['userId'])) {
            $project = $project->where('user_id', $postData['userId']);
            $pojectDocumentArr['created_by'] = $postData['userId'];
        }
        $project = $project->first();
        if (empty($project)) {
            $dataArr['success'] = false;
            $dataArr['message'] = config('message.front_user.project_not_found');
            return $dataArr;
        }
        if ($postData['dataArr']['type'] == 'cover_image') {

            if (isset($postData['document']) && !empty($postData['document'])) {
                $document = fileUploadPublic($postData['document'], config('constants.user_project_path'));
                $uplopadedDoc = asset(config('constants.user_project_path') . '/' . $document);
            }
            Project::where('id', $id)->update([
                'default_image' => $document
            ]);
        } else {
            $pojectDocumentArr['project_id'] = $id;
            $pojectDocumentArr['document_type'] = $postData['dataArr']['type'];
            if (isset($postData['document']) && !empty($postData['document'])) {
                $document = fileUploadPublic($postData['document'], config('constants.user_project_path'));
                $pojectDocumentArr['document_name'] = $document;
                $pojectDocumentArr['document_original_name'] = $postData['document']->getClientOriginalName();
                $uplopadedDoc = asset(config('constants.user_project_path') . '/' . $document);
                $document = $postData['document']->getClientOriginalName();
            }
            $projectArr = ProjectDocument::create($pojectDocumentArr);
        }

        $response['document'] = $uplopadedDoc ?? '';
        $response['document_name'] = $document ?? '';
        if ($postData['dataArr']['type'] == 'cover_image') {
            $response['doc_id'] = '';
            $message = config('message.front_user.project_cover_image_add_success');
        } else {
            $response['doc_id'] = encryptId($projectArr->id);
            $message = config('message.front_user.project_docs_add_success');
            if ($postData['dataArr']['type'] == 'image') {
                $message = config('message.front_user.project_cover_image_add_success');
            }
        }

        $response['type'] =  $postData['dataArr']['type'];

        $dataArr['response'] = $response;
        $dataArr['message'] = $message;
        return $dataArr;
    }

    public function deleteProjectDocument($paramsArr = [])
    {
        $response = [];
        $dataArr = [
            'response' => $response,
            'success' => true,
            'message' => '',
        ];
        $id = decryptId($paramsArr['id']);
        $projectDoc = ProjectDocument::find($id);
        if (empty($projectDoc)) {
            $dataArr['success'] = false;
            $dataArr['message'] = config('message.front_user.project_doc_not_found');
            return $dataArr;
        }
        $projectDocName = $projectDoc->document_name;
        fileDeletePublic($projectDocName, config('constants.user_project_path'));
        $dataArr['message'] = config('message.front_user.project_doc_delete_success');
        if (isset($projectDoc->document_type) && $projectDoc->document_type == 'image') {
            $dataArr['message'] = config('message.front_user.project_cover_image_delete_success');
        }
        $projectDoc->delete();
        return $dataArr;
    }

    public function deleteProjectCoverImage($paramsArr = [])
    {
        $response = [];
        $dataArr = [
            'response' => $response,
            'success' => true,
            'message' => '',
        ];
        $id = $paramsArr['id'];
        $project = Project::find($id);
        if (empty($project)) {
            $dataArr['success'] = false;
            $dataArr['message'] = config('message.front_user.project_doc_not_found');
            return $dataArr;
        }
        $projectDocName = $project->default_image;
        fileDeletePublic($projectDocName, config('constants.user_project_path'));
        $project->default_image = '';
        $project->save();
        $dataArr['message'] = config('message.front_user.project_cover_image_delete_success');
        return $dataArr;
    }

    public function recentReviewProjectsList()
    {
        $response = [];
        $data = ProjectComment::with('hasProject', 'hasUser')->limit('3')->orderBy('id', 'DESC')->get();
        if (isset($data) && !empty($data)) {
            foreach ($data as $recentProject) {
                $recentProjArr = [];
                $recentProjArr['project_name'] = $recentProject->hasProject->title ?? '';
                $recentProjArr['comment'] = $recentProject->comment ?? '';
                $recentProjArr['donor_name'] = $recentProject->hasUser->name ?? '';
                $recentProjArr['rating'] = $recentProject->rating ?? '';
                $defaultImg = checkFileExitOrNot(
                    $recentProject->hasUser->image,
                    public_path(config('constants.user_profile_img_path'))
                );
                $recentProjArr['image'] = $defaultImg === true
                    ? asset(config('constants.user_profile_img_path') . '/' . $recentProject->hasUser->image)
                    : asset(config('constants.no_user_image_path'));
                $response[] = $recentProjArr;
            }
        }
        return $response;
    }

    public function userTotalDonations()
    {
        $user = jwtAuthUser();
        $userId = $user->id;
        return ProjectDonation::withCount([
            'hasTransactions AS totalAmount' => function ($transaction) {
                $transaction->where('type', 'Donation')->select(DB::raw("SUM(amount)"));
            }
        ])->where('user_id', $userId)->pluck('totalAmount')->sum();
    }

    public function getRatingStartsHTML($review)
    {
        $stars = '';
        $halfRating = 0.5;
        for ($i = 0; $i < 5; $i++) {
            if ($review > $i) {
                if ($review < $halfRating) {
                    $stars .=
                        '<li>
                        <span class="material-symbols-outlined">star_rate_half</span>
                    </li>';
                } elseif ($review >= $halfRating) {
                    $stars .=
                        '<li>
                        <span class="material-symbols-sharp"> star </span>
                    </li>';
                }
            } else {
                $stars .= '<li><span class="material-symbols-outlined">star</span></li>';
            }
            $halfRating = $halfRating + 1;
        }

        return $stars;
    }

    public function getAllReviews($projArr = [])
    {
        if (empty($projArr)) {
            return $dataArr['success'] = false;
        }
        $proAllReview = ProjectReview::query();
        $proAllReview = $proAllReview->where('project_id', $projArr['project_id']);
        $proAllReview = $proAllReview->paginate(env('PAGINATION_COUNT'));
        $response = [];
        if (!empty($proAllReview)) {
            $allReview = $proAllReview;
            $i = 0;
            $page = $projArr['page'];
            foreach ($allReview as $k => $reviews) {
                $response['all_reviews'][$i]['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                $response['all_reviews'][$i]['rating'] = $reviews->rating;
                $response['all_reviews'][$i]['starsHTML'] = $this->getRatingStartsHTML($reviews->rating);
                $response['all_reviews'][$i]['user_id'] = $reviews->user_id;
                $user = User::find($reviews->user_id);
                $response['all_reviews'][$i]['user_name'] = $user->name;
                $response['all_reviews'][$i]['profile_image'] = (isset($user->image) && $user->image != '') ?
                asset(config('constants.user_profile_img_path') . '/' . $user->image) : asset(config('constants.no_user_image_path'));
                $response['all_reviews'][$i]['comment'] = $reviews->comment;
                $response['all_reviews'][$i]['created_at'] = $reviews->created_at;
                $i++;
            }
        }
        $paginateData = $proAllReview->toArray();
        $pagination =  pagination($paginateData);
        $dataArr = [
            'response' => $response,
            'pagination' => $pagination,
            'success' => true
        ];
        if ($proAllReview->count() == 0) {
            $dataArr['success'] = false;
        }
        return $dataArr;
    }

    public function getProjectFollowersList($projArr = [])
    {
        if (empty($projArr)) {
            return $dataArr['success'] = false;
        }
        $followers = ProjectFollow::query();
        if (isset($projArr['project_ids']) && !empty($projArr['project_ids'])) {
            $followers = $followers->whereIn('project_id', $projArr['project_ids']);
        }
        $followers = $followers->orderBy('created_at', 'DESC');
        $followers = $followers->paginate(env('PAGINATION_COUNT'));
        $response = [];
        if (!empty($followers)) {
            $i = 0;
            $page = $projArr['page'];
            foreach ($followers as $k => $item) {
                $response[$i]['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                $response[$i]['user_id'] = $item->user_id;

                $user = User::find($item->user_id);
                $response[$i]['user_name'] = $user->name;
                $response[$i]['email'] = $user->email;
            }
        }
        $paginateData = $followers->toArray();
        $pagination =  pagination($paginateData);
        $dataArr = [
            'response' => $response,
            'pagination' => $pagination,
            'success' => true
        ];
        if ($followers->count() == 0) {
            $dataArr['success'] = false;
        }
        return $dataArr;
    }

    public function getProjectIdFromUserId($userId)
    {
        $project = Project::where('user_id', $userId);
        $projectIds = [0];
        if (!empty($project->get()->toArray())) {
            $projectIds = array_column($project->get()->toArray(), 'id');
        }
        return $projectIds;
    }

    public function sponserChart($filter)
    {
        $user = jwtAuthUser();
        $projectIds = $this->getProjectIdFromUserId($user->id);
        $years = [];
        $yearsArray = [];
        $currentYear = date('Y');
        if (isset($filter['searchType']) && $filter['searchType'] == 'month') {
            $donation = ProjectDonation::query();
            $donation = $donation->select(DB::raw('MONTH(created_at) as month'), DB::raw("SUM(donation_amount)"));
            (isset($filter['user']) && $filter['user'] == 1) ?
            $donation =  $donation->whereIn('project_id', $projectIds)
                : '';
            $corporateUser = $donation->whereHas('hasUser', function ($query) {
                $query->where('user_type', 2);
            });
            $donation =  $donation->whereYear('created_at', $currentYear);
            $corporateUser = $corporateUser->groupBy(DB::raw('MONTH(created_at)'))->get()->toArray();
            $corporateUserSum = $this->setDataForChart($corporateUser);

            $donationIndi = ProjectDonation::query();
            $donationIndi = $donationIndi->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw("SUM(donation_amount)")
            );
            if (isset($filter['user']) && $filter['user'] == 1) {
                $donationIndi = $donationIndi->whereIn('project_id', $projectIds);
            }
            $donationIndi =  $donationIndi->whereYear('created_at', $currentYear);
            $individualUser = $donationIndi->whereHas('hasUser', function ($query) {
                $query->where('user_type', 1);
            });
            $individualUser = $individualUser->groupBy(DB::raw('MONTH(created_at)'))->get()->toArray();
            $individualSum = $this->setDataForChart($individualUser);
        } elseif (isset($filter['searchType']) && $filter['searchType'] == 'year') {

            $donation = ProjectDonation::query();
            $donation = $donation->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw("SUM(donation_amount) as donation")
            );
            if (isset($filter['user']) && $filter['user'] == 1) {
                $donation = $donation->whereIn('project_id', $projectIds);
            }
            $corporateUser = $donation->whereHas('hasUser', function ($query) {
                $query->where('user_type', 2);
            });
            $corporateUser = $corporateUser->groupBy(DB::raw('YEAR(created_at)'))->get()->toArray();

            $donationIndi = ProjectDonation::query();
            $donationIndi = $donationIndi->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw("SUM(donation_amount) as donation")
            );
            if (isset($filter['user']) && $filter['user'] == 1) {
                $donationIndi = $donationIndi->whereIn('project_id', $projectIds);
            }
            $individualUser = $donationIndi->whereHas('hasUser', function ($query) {
                $query->where('user_type', 1);
            });
            $individualUser = $individualUser->groupBy(DB::raw('YEAR(created_at)'))->get()->toArray();
            
            $years = array_unique(
                array_merge(
                    array_column($corporateUser, 'year'),
                    array_column($individualUser, 'year')
                )
            );
            foreach($years as $year){
                $yearsArray[]=$year;
            }
            $yearsArray=array_reverse($yearsArray);
            $individualSum = array_reverse($this->setYearDataForChart($individualUser, $years));
            $corporateUserSum = array_reverse($this->setYearDataForChart($corporateUser, $years));
        }
        $individualSponserCount = ProjectDonation::query();
        (isset($filter['user']) && $filter['user'] == 1)
            ? $individualSponserCount = $individualSponserCount->whereIn('project_id', $projectIds)
            : '';
        $individualSponserCount = $individualSponserCount->whereHas('hasUser', function ($query) {
            $query->where('user_type', 1);
        })->groupBy('user_id')->get()->count();

        $corpoSponserCount = ProjectDonation::query();
        (isset($filter['user']) && $filter['user'] == 1)
            ? $corpoSponserCount = $corpoSponserCount->whereIn('project_id', $projectIds)
            : '';
        $corpoSponserCount = $corpoSponserCount->whereHas('hasUser', function ($query) {
            $query->where('user_type', 2);
        })->groupBy('user_id')->get()->count();

        $individualSponserDonation = ProjectDonation::query();
        (isset($filter['user']) && $filter['user'] == 1)
            ? $individualSponserDonation = $individualSponserDonation->whereIn('project_id', $projectIds)
            : '';

        $individualSponserDonation = $individualSponserDonation->whereHas('hasUser', function ($query) {
            $query->where('user_type', 1);
        })->select(DB::raw('SUM(donation_amount) as donation'))->get()->toArray();

        $individualDonation = 0;
        if (!empty($individualSponserDonation &&
            $individualSponserDonation[0] &&
            $individualSponserDonation[0]['donation'])) {
            $individualDonation = $individualSponserDonation[0]['donation'];
        }

        $corpoSponserDonation = ProjectDonation::query();
        (isset($filter['user']) && $filter['user'] == 1)
            ? $corpoSponserDonation = $corpoSponserDonation->whereIn('project_id', $projectIds)
            : '';
        $corpoSponserDonation = $corpoSponserDonation->whereHas('hasUser', function ($query) {
            $query->where('user_type', 2);
        })->select(DB::raw('SUM(donation_amount) as donation'))->get()->toArray();

        $corpoDonation = 0;
        if (!empty($corpoSponserDonation &&
            $corpoSponserDonation[0] &&
            $corpoSponserDonation[0]['donation'])) {
            $corpoDonation = $corpoSponserDonation[0]['donation'];
        }

        $social = User::query();
        $social = $social->whereHas('hasUserDetails', function ($query) {
            $query->whereIn('twitter', [2, 3]);
            $query->orWhereIn('facebook', [2, 3]);
            $query->orWhereIn('snapchat', [2, 3]);
            $query->orWhereIn('instagram', [2, 3]);
            $query->orWhereIn('linkedin', [2, 3]);
        })->count();

        return [
            'individual' => $individualSum,
            'corporate' => $corporateUserSum,
            'year' => $yearsArray,
            'individualSponserCount' => $individualSponserCount,
            'corpoSponserCount' => $corpoSponserCount,
            'totalSponser' => $individualSponserCount + $corpoSponserCount,
            'totalDonationByIndividual' => $individualDonation,
            'totalDonationByCorpo' => $corpoDonation,
            'social' => $social,
            'totalDonation' => array_sum($individualSum) + array_sum($corporateUserSum)
        ];
    }

    public function setDataForChart($array)
    {
        $months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $monthArray = [];
        foreach ($months as $month) {
            if (array_search($month, array_column($array, 'month')) !== false) {
                $key = array_search($month, array_column($array, 'month'));
                $monthArray[] = $array[$key]['SUM(donation_amount)'];
            } else {
                $monthArray[] = 0;
            }
        }
        return $monthArray;
    }
    public function setDataForUserChart($array)
    {
        $months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $monthArray = [];
        foreach ($months as $month) {
            if (array_search($month, array_column($array, 'month')) !== false) {
                $key = array_search($month, array_column($array, 'month'));
                $monthArray[] = $array[$key]['count'];
            } else {
                $monthArray[] = 0;
            }
        }
        return $monthArray;
    }
    public function setYearDataForChart($array, $years)
    {
        $yearArray = [];
        foreach ($years as $year) {
            if (array_search($year, array_column($array, 'year')) !== false) {
                $key = array_search($year, array_column($array, 'year'));
                $yearArray[] = $array[$key]['donation'];
            } else {
                $yearArray[] = 0;
            }
        }
        return $yearArray;
    }

    public function userChart($filter)
    {
        $threeMonthsAgo = Carbon::now()->subMonths(3);
        if (isset($filter['searchType']) && $filter['searchType'] == 'week') {
            // Calculate the date range for the last 7 days
            $startDate = Carbon::now()->subDays(7)->startOfDay(); // Start of the last 7 days
            $endDate = Carbon::now()->endOfDay();

            // End of the current day
            // Retrieve the count of users created on each weekday for the last 7 days
            $userCounts = DB::table('users')
                ->selectRaw('DAYNAME(created_at) as weekday, COUNT(*) as count, created_at')
                ->whereBetween('created_at', [$startDate, $endDate]);
            if (isset($filter['status']) && $filter['status'] == 'active') {
                $userCounts = $userCounts->where('last_logged_in', '>=', $threeMonthsAgo);
            } elseif (isset($filter['status']) && $filter['status'] == 'inactive') {
                $userCounts = $userCounts->where('last_logged_in', '<', $threeMonthsAgo);
            }
            $userCounts = $userCounts->where('is_signup_completed', 1);
            $userCounts = $userCounts->groupBy('weekday')
                ->orderBy('created_at')
                ->get();

            // Create an array to hold the weekday-wise user counts
            $weekdayCounts = [];
            $weekdayCountsData = [];
            $weekdayLabels = [];
            $weekDays = $this->setWeekDays();
            foreach ($weekDays as $day) {
                $weekdayCounts[$day] = 0;
            }
            foreach ($userCounts as $userCount) {
                $weekdayCounts[$userCount->weekday] = $userCount->count;
            }
            foreach ($weekdayCounts as $key => $val) {
                array_push($weekdayLabels, $key);
                array_push($weekdayCountsData, $val);
            }
            return [
                'success' => true,
                'label' => $weekdayLabels,
                'data' => $weekdayCountsData,
                'totalUser' => array_sum($weekdayCountsData)
            ];
        } elseif (isset($filter['searchType']) && $filter['searchType'] == 'month') {
            $currentDate = Carbon::now();
            $dateSixMonthsAgo = $currentDate->copy()->subMonths(6);
            $currentMonth = $dateSixMonthsAgo->copy()->startOfMonth();
            $endMonth = $currentDate->copy()->startOfMonth()->addMonth();

            $userCounts = DB::table('users')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count, created_at')
                ->whereBetween('created_at', [$currentMonth, $endMonth]);
            if (isset($filter['status']) && $filter['status'] == 'active') {
                $userCounts = $userCounts->where('last_logged_in', '>=', $threeMonthsAgo);
            } elseif (isset($filter['status']) && $filter['status'] == 'inactive') {
                $userCounts = $userCounts->where('last_logged_in', '<', $threeMonthsAgo);
            }
            $userCounts = $userCounts->where('is_signup_completed', 1);
            $userCounts = $userCounts->groupBy('month')
                ->orderBy('created_at')
                ->get();

            $monthCounts = [];
            // Loop through the users and populate the month counts array
            foreach ($userCounts as $user) {
                $monthCounts[$user->month] = $user->count;
            }
            // Generate the months and counts array for the last 6 months
            $lastSixMonths = [];
            $lastSixMonthsLabel = [];
            $currentMonth = $dateSixMonthsAgo->copy()->startOfMonth();
            $endMonth = $currentDate->copy()->startOfMonth()->addMonth();
            while ($currentMonth < $endMonth) {
                $month = $currentMonth->format('n');
                $count = isset($monthCounts[$month]) ? $monthCounts[$month] : 0;
                array_push($lastSixMonths, $count);
                array_push($lastSixMonthsLabel, $currentMonth->format('M'));
                $currentMonth->addMonth();
            }

            return [
                'success' => true,
                'label' => $lastSixMonthsLabel,
                'data' => $lastSixMonths,
                'totalUser' => array_sum($lastSixMonths)
            ];
        } elseif (isset($filter['searchType']) && $filter['searchType'] == 'year') {
            $startDate = Carbon::now()->subYears(6)->startOfYear(); // Start of the first year in the range
            $endDate = Carbon::now()->endOfYear();                 // End of the current year

            // Retrieve the count of users created in each year for the last 6 years
            $userCounts = DB::table('users')
                ->selectRaw('YEAR(created_at) as year, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate]);
            if (isset($filter['status']) && $filter['status'] == 'active') {
                $userCounts = $userCounts->where('last_logged_in', '>=', $threeMonthsAgo);
            } elseif (isset($filter['status']) && $filter['status'] == 'inactive') {
                $userCounts = $userCounts->where('last_logged_in', '<', $threeMonthsAgo);
            }
            $userCounts = $userCounts->where('is_signup_completed', 1);
            $userCounts = $userCounts->groupBy('year')
                ->orderBy('year')
                ->get();

            // Create an array to hold the year-wise user counts
            $yearCounts = [];

            // Initialize the year counts array with 0 for all years
            for ($year = $startDate->year; $year <= $endDate->year; $year++) {
                $yearCounts[$year] = 0;
            }
            foreach ($userCounts as $userCount) {
                $year = $userCount->year;
                $count = $userCount->count;
                $yearCounts[$year] = $count;
            }

            $lastSixMonths = [];
            $lastSixMonthsLabel = [];
            foreach ($yearCounts as $key => $val) {
                array_push($lastSixMonths, $val);
                array_push($lastSixMonthsLabel, $key);
            }
            return [
                'success' => true,
                'label' => $lastSixMonthsLabel,
                'data' => $lastSixMonths,
                'totalUser' => array_sum($lastSixMonths)
            ];
        }
        return [
            'success' => false,
            'label' => [],
            'data' => [],
            'totalUser' => 0
        ];
    }

    public function setWeekDays()
    {
        $currentDate = Carbon::now();
        // Get the date 7 days ago
        $sevenDaysAgo = $currentDate->subDays(7)->addDay();
        $last7Days = [];
        // Display the last 7 days
        for ($i = 0; $i < 7; $i++) {
            $last7Days[] = $sevenDaysAgo->format('l');
            $sevenDaysAgo->addDay();
        }
        return $last7Days;
    }

    public function setMonth()
    {
        $currentDate = Carbon::now();
        // Get the date 7 days ago
        $dateSixMonthsAgo = $currentDate->copy()->subMonths(6);
        $currentMonth = $dateSixMonthsAgo->copy()->startOfMonth();
        $last7Month = [];
        // Display the last 7 days
        for ($i = 0; $i < 6; $i++) {
            $last7Month[] = $currentMonth->format('n');
            $currentMonth->addMonth();
        }
        return $last7Month;
    }

    public function userProjectDashboard($filter)
    {
        $user = jwtAuthUser();
        $userId = $user->id;
        $paramsArr['project_ids'] = [];
        $response = [];
        $paramsArr['project_followed_ids'] = [];

        $project = Project::select('id');
        (isset($filter['user']) && $filter['user'] == 1)
            ? $project = $project->where('user_id', $userId) : '';

        $project = $project->get()->toArray();
        if (!empty($project)) {
            foreach ($project as $key => $value) {
                $paramsArr['project_ids'][$key] = $value['id'];
            }
        }

        $donation = ProjectDonation::query();
        if (isset($filter['user']) && $filter['user'] == 1) {
            $donation = $donation->whereIn('project_id', $paramsArr['project_ids']);
        }

        $donation = $donation->orderBy('created_at', 'DESC')->limit(3)->get()->toArray();
        $response['my_project_donations'] = $donation;

        if (!empty($response['my_project_donations'])) {
            foreach ($response['my_project_donations'] as $key => $val) {
                $user = User::find($val['user_id']);
                $response['my_project_donations'][$key]['user_name'] = $user->name;

                $response['my_project_donations'][$key]['profile_image'] = (isset($user->image) && $user->image != '')
                    ? asset(config('constants.user_profile_img_path') . '/' . $user->image)
                    : asset(config('constants.no_user_image_path'));

                $project = Project::find($val['project_id']);
                $response['my_project_donations'][$key]['project_title'] = $project->title;
            }
        }

        $review = ProjectReview::query();
        (isset($filter['user']) && $filter['user'] == 1)
            ? $review = $review->whereIn('project_id', $paramsArr['project_ids']) : '';

        $review = $review->orderBy('created_at', 'DESC')->limit(4)->get()->toArray();
        $response['my_project_ratings'] = $review;

        if (!empty($response['my_project_ratings'])) {
            foreach ($response['my_project_ratings'] as $key => $val) {
                $response['my_project_ratings'][$key]['starsHTML'] =
                    $this->getRatingStartsHTML($val['rating']);

                $project = Project::find($val['project_id']);
                $response['my_project_ratings'][$key]['project_title'] = $project->title;

                $user = User::find($val['user_id']);
                $response['my_project_ratings'][$key]['user_name'] = $user->name;

                $response['my_project_ratings'][$key]['profile_image'] = (isset($user->image) && $user->image != '')
                    ? asset(config('constants.user_profile_img_path') . '/' . $user->image)
                    : asset(config('constants.no_user_image_path'));
            }
        }

        if (isset($filter['user']) && $filter['user'] == 1) {
            $favouriteProjectIds = ProjectFollow::select('project_id')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->get()
                ->toArray();
            if (!empty($favouriteProjectIds)) {
                foreach ($favouriteProjectIds as $key => $value) {
                    $paramsArr['project_followed_ids'][$key] = $value['project_id'];
                }
            }
            $favPro = Project::query();
            $favPro = $favPro->whereIn('id', $paramsArr['project_followed_ids']);
            $favPro = $favPro->withCount([
                'hasDonations AS totalAmount' => function ($transaction) {
                    $transaction->select(DB::raw("SUM(donation_amount)"));
                }
            ])->get();
            $response['favourite_projects'] = $favPro;
            if (!empty($favPro->toArray())) {
                foreach ($favPro->toArray() as $key => $value) {
                    $percentageAmount = 0;
                    if ($value['amount'] && $value['amount'] != null && $value['amount'] > 0) {
                        $percentageAmount = $value['totalAmount'] * 100 / $value['amount'];
                    }
                    $percentageAmount = number_format((float)$percentageAmount, 2, '.', '');
                    $response['favourite_projects'][$key]['totalAmount'] =
                        $response['favourite_projects'][$key]['totalAmount'] ?? 0;
                    $response['favourite_projects'][$key]['totalDonationPercentage'] =
                        $percentageAmount;
                    $response['favourite_projects'][$key]['leftAmount'] =
                        $response['favourite_projects'][$key]['amount'] -
                        $response['favourite_projects'][$key]['totalAmount'];
                    $response['favourite_projects'][$key]['default_image'] =
                        ($value['default_image'] != '')
                        ?  asset(config('constants.user_project_path') . '/' . $value['default_image'])
                        : '';
                }
            }
        } else {
            $response['favourite_projects'] = [];
        }

        $recentUsers = User::where('is_signup_completed', '1')
            ->with('hasUserDetails')
            ->orderBy('created_at', 'DESC')
            ->limit(4)
            ->get()
            ->toArray();
        $users = [];
        foreach ($recentUsers as $key => $value) {
            $users[$key]['name'] = $value['name'];
            $users[$key]['location'] = $value['has_user_details']['location'];
            $users[$key]['user_type'] = $value['user_type'] ?? '';
            $users[$key]['mobile'] = $value['has_user_details']['contact_number'] ?? '';
            $users[$key]['image'] = (!empty($value['image']))
                ? asset(config('constants.user_profile_img_path') . '/' . $value['image'])
                : asset(config('constants.no_user_image_path'));
            $users[$key]['email'] = $value['email'];
        }
        $response['recent_users'] = $users;
        return $response;
    }

    public function dashboardMap($filter)
    {
        $response = [];
        $user = jwtAuthUser();
        $projectIds = $this->getProjectIdFromUserId($user->id);

        $userTotalProject = (isset($filter['user']) && $filter['user'] == 1)
            ? Project::where('user_id', $user->id)->count()
            : Project::all()->count();

        if (isset($filter['searchType']) && $filter['searchType'] == 'project') {
            $results = DB::table('countries')
                ->join('projects', 'projects.country', '=', 'countries.id');
            if (isset($filter['user']) && $filter['user'] == 1) {
                $results = $results->whereIn('projects.id', $projectIds);
            }
            $results = $results->select(
                'countries.id',
                'countries.name',
                'countries.code',
                (DB::raw('COALESCE(COUNT(projects.id),0) as project_count'))
            )
                ->groupBy('countries.id', 'countries.name')
                ->get()
                ->toArray();
            $response['country_project_count'] = [];
            $response['country_name_project_count'] = [];
            if (!empty($results)) {
                foreach ($results as $key => $val) {
                    $i = 0;
                    $response['country_project_count'][$key][$i] = $val->code;
                    $response['country_project_count'][$key][++$i] = $val->project_count;
                }
                $response['country_project_count'] = changeCountryArray('code', $response['country_project_count']);
            } else {
                $response['country_project_count'] = emptyCountryArray('code');
            }
            if (!empty($results)) {
                foreach ($results as $key => $val) {
                    $i = 0;
                    $response['country_name_project_count'][$key][$i] = $val->name;
                    $response['country_name_project_count'][$key][++$i] = $val->project_count;
                }
                $response['country_name_project_count'] =
                    changeCountryArray('name', $response['country_name_project_count']);
            } else {
                $response['country_name_project_count'] = emptyCountryArray('name');
            }

            if (isset($filter['valueType']) && $filter['valueType'] == 'percentage') {
                $response['country_project_count'] =
                    convertToPercentage($response['country_project_count'], $userTotalProject);
                $response['country_name_project_count'] =
                    convertToPercentage($response['country_name_project_count'], $userTotalProject);
            }
        } else {
            $totalDonation = 1;
            (isset($filter['user']) && $filter['user'] == 1)
                ?  $userTotalDonation = ProjectDonation::select(
                    DB::raw("SUM(donation_amount) as donation_count")
                )->whereIn('project_id', $projectIds)->get()->toArray()
                :
                ProjectDonation::select(
                    DB::raw("SUM(donation_amount) as donation_count")
                )->get()->toArray();

            if (
                isset($userTotalDonation) &&
                isset($userTotalDonation[0]) &&
                isset($userTotalDonation[0]['donation_count'])
            ) {
                $totalDonation = $userTotalDonation[0]['donation_count'];
            }
            $results = DB::table('project_donations')
                ->join('projects', 'project_donations.project_id', '=', 'projects.id')
                ->join('countries', 'projects.country', '=', 'countries.id');

            (isset($filter['user']) && $filter['user'] == 1)
                ? $results = $results->whereIn('project_donations.project_id', $projectIds)
                : '';

            $results = $results->select(
                'countries.id',
                'countries.name',
                'countries.code',
                DB::raw("SUM(donation_amount) as donation_count")
            )
                ->groupBy('countries.id', 'countries.name')
                ->get()
                ->toArray();

            $response['country_donation_count'] = [];
            $response['country_name_donation_count'] = [];
            if (!empty($results)) {
                foreach ($results as $key => $val) {
                    $i = 0;
                    $response['country_donation_count'][$key][$i] = $val->code;
                    $response['country_donation_count'][$key][++$i] = $val->donation_count;
                }
                $response['country_donation_count'] = changeCountryArray('code', $response['country_donation_count']);
            } else {
                $response['country_donation_count'] = emptyCountryArray('code');
            }
            if (!empty($results)) {
                foreach ($results as $key => $val) {
                    $i = 0;
                    $response['country_name_donation_count'][$key][$i] = $val->name;
                    $response['country_name_donation_count'][$key][++$i] = $val->donation_count;
                }
                $response['country_name_donation_count'] =
                    changeCountryArray('name', $response['country_name_donation_count']);
            } else {
                $response['country_name_donation_count'] = emptyCountryArray('name');
            }
            if (
                isset($filter['valueType']) &&
                $filter['valueType'] == 'percentage'
            ) {
                $response['country_donation_count'] =
                    convertToPercentage($response['country_donation_count'], $totalDonation);
                $response['country_name_donation_count'] =
                    convertToPercentage($response['country_name_donation_count'], $totalDonation);
            }
        }
        if (!empty($response)) {
            return [
                'success' => true,
                'response' => $response
            ];
        } else {
            return [
                'success' => false,
                'response' => null
            ];
        }
    }

    public function countsForDashboardCards($filter)
    {
        $user = jwtAuthUser();
        $isAdmin = (isset($filter['user']) && $filter['user'] == 0) ? true : false;
        $project = Project::where('user_id', $user->id);
        $projectIds = array_column($project->get()->toArray(), 'id');
        $response = [];
        $response['total_project'] = (!$isAdmin)
            ? $project->count()
            :  Project::all()->count();

        $response['total_donation'] = 0;
        $totalDonation = ProjectDonation::select(DB::raw("SUM(donation_amount) AS donation"));
        (!$isAdmin) ? $totalDonation = $totalDonation->whereIn('project_id', $projectIds) : '';

        $totalDonation = $totalDonation->get()->toArray();
        if (!empty($totalDonation) && isset($totalDonation[0]) && isset($totalDonation[0]['donation'])) {
            $response['total_donation'] = $totalDonation[0]['donation'];
        }

        $totalVolunteer = (!$isAdmin)
            ? ProjectVolunteer::whereIn('project_id', $projectIds)->count()
            : ProjectVolunteer::all()->count();

        $response['total_volunteer'] = $totalVolunteer;

        $totalUsers = 0;
        $totalUsers = User::where('is_signup_completed', '1')->count();
        $response['total_users'] = $totalUsers;

        //Userr card chart: start
        $currentDate = Carbon::now();
        // Calculate the date 6 months ago from now
        $dateSixMonthsAgo = $currentDate->copy()->subMonths(6);
        // Retrieve users created within the last 6 months and group them by months
        $users = DB::table('users')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('is_signup_completed', 1)
            ->whereBetween('created_at', [$dateSixMonthsAgo, $currentDate])
            ->groupBy('month')
            ->get();
        // Create an array to hold the month counts
        $monthCounts = [];
        // Loop through the users and populate the month counts array
        foreach ($users as $user) {
            $monthCounts[$user->month] = $user->count;
        }
        // Generate the months and counts array for the last 6 months
        $lastSixMonths = [];
        $lastSixMonthsLabel = [];
        $currentMonth = $dateSixMonthsAgo->copy()->startOfMonth();
        $endMonth = $currentDate->copy()->startOfMonth();
        while ($currentMonth <= $endMonth) {
            $month = $currentMonth->format('n');
            $count = isset($monthCounts[$month]) ? $monthCounts[$month] : 0;
            array_push($lastSixMonths, $count);
            array_push($lastSixMonthsLabel, $currentMonth->format('M'));
            $currentMonth->addMonth();
        }

        $totalUserBefore6Months = $totalUsers - array_sum($lastSixMonths);
        $lastSixMonthsData = [];
        foreach ($lastSixMonths as $data) {
            $totalUserBefore6Months += $data;
            array_push($lastSixMonthsData, $totalUserBefore6Months);
        }
        $response['users_chart']['label'] = $lastSixMonthsLabel;
        $response['users_chart']['data'] = $lastSixMonths;
        // $response['users_chart']['data'] = $lastSixMonthsData;
        //Userr card chart: end

        //Volunteer card chart: start
        $volunteers = DB::table('project_volunteers')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count');
        if (!$isAdmin) {
            $volunteers = $volunteers->whereIn('project_id', $projectIds);
        }
        $volunteers = $volunteers->whereBetween('created_at', [$dateSixMonthsAgo, $currentDate])
            ->groupBy('month')
            ->get();
        // Create an array to hold the month counts
        $monthCounts = [];
        // Loop through the users and populate the month counts array
        foreach ($volunteers as $volunteer) {
            $monthCounts[$volunteer->month] = $volunteer->count;
        }
        // Generate the months and counts array for the last 6 months
        $lastSixMonthsVol = [];
        $lastSixMonthsLabelVol = [];
        $currentMonth = $dateSixMonthsAgo->copy()->startOfMonth();
        $endMonth = $currentDate->copy()->startOfMonth();
        while ($currentMonth <= $endMonth) {
            $month = $currentMonth->format('n');
            $count = isset($monthCounts[$month]) ? $monthCounts[$month] : 0;
            array_push($lastSixMonthsVol, $count);
            array_push($lastSixMonthsLabelVol, $currentMonth->format('M'));
            $currentMonth->addMonth();
        }
        $totalVolBefore6Months = $totalVolunteer - array_sum($lastSixMonthsVol);

        $lastSixMonthsDataVol = [];
        foreach ($lastSixMonthsVol as $data) {
            $totalVolBefore6Months += $data;
            array_push($lastSixMonthsDataVol, $totalVolBefore6Months);
        }
        $response['volunteer_chart']['label'] = $lastSixMonthsLabelVol;
        $response['volunteer_chart']['data'] = $lastSixMonthsVol;
        // $response['volunteer_chart']['data'] = $lastSixMonthsDataVol;
        //Volunteer card chart: end

        //Donation card chart: start
        $donations = DB::table('project_donations')
            ->selectRaw('MONTH(created_at) as month, SUM(donation_amount) AS donation');
        (!$isAdmin) ? $donations = $donations->whereIn('project_id', $projectIds) : '';
        $donations = $donations->whereBetween('created_at', [$dateSixMonthsAgo, $currentDate])
            ->groupBy('month')
            ->get();

        // Create an array to hold the month counts
        $monthCounts = [];
        // Loop through the users and populate the month counts array
        foreach ($donations as $donation) {
            $monthCounts[$donation->month] = $donation->donation;
        }

        // Generate the months and counts array for the last 6 months
        $lastSixMonthsDon = [];
        $lastSixMonthsLabelDon = [];
        $currentMonth = $dateSixMonthsAgo->copy()->startOfMonth();
        $endMonth = $currentDate->copy()->startOfMonth();
        while ($currentMonth <= $endMonth) {
            $month = $currentMonth->format('n');
            $count = isset($monthCounts[$month]) ? $monthCounts[$month] : 0;
            array_push($lastSixMonthsDon, $count);
            array_push($lastSixMonthsLabelDon, $currentMonth->format('M'));
            $currentMonth->addMonth();
        }

        $totalDonation = $response['total_donation'] ?? 0;
        $totalDonBefore6Months = $totalDonation - array_sum($lastSixMonthsDon);

        $lastSixMonthsDataDon = [];
        foreach ($lastSixMonthsDon as $data) {
            $totalDonBefore6Months += $data;
            array_push($lastSixMonthsDataDon, $totalDonBefore6Months);
        }
        $response['donation_chart']['label'] = $lastSixMonthsLabelDon;
        $response['donation_chart']['data'] = $lastSixMonthsDon;
        // $response['donation_chart']['data'] = $lastSixMonthsDataDon;
        //Donation card chart: end
        $user = jwtAuthUser();
        //Project card chart: start
        $projectsChart = DB::table('projects')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count');

        (!$isAdmin) ? $projectsChart = $projectsChart->where('user_id', $user->id) : '';

        $projectsChart = $projectsChart->whereBetween('created_at', [$dateSixMonthsAgo, $currentDate])
            ->groupBy('month')
            ->get();

        // Create an array to hold the month counts
        $monthCounts = [];
        // Loop through the users and populate the month counts array
        foreach ($projectsChart as $project) {
            $monthCounts[$project->month] = $project->count;
        }
        // Generate the months and counts array for the last 6 months
        $lastSixMonthsPro = [];
        $lastSixMonthsLabelPro = [];
        $currentMonth = $dateSixMonthsAgo->copy()->startOfMonth();
        $endMonth = $currentDate->copy()->startOfMonth();
        while ($currentMonth <= $endMonth) {
            $month = $currentMonth->format('n');
            $count = isset($monthCounts[$month]) ? $monthCounts[$month] : 0;
            array_push($lastSixMonthsPro, $count);
            array_push($lastSixMonthsLabelPro, $currentMonth->format('M'));
            $currentMonth->addMonth();
        }

        $totalProBefore6Months = $response['total_project'] - array_sum($lastSixMonthsPro);

        $lastSixMonthsDataPro = [];
        foreach ($lastSixMonthsPro as $data) {
            $totalProBefore6Months += $data;
            array_push($lastSixMonthsDataPro, $totalProBefore6Months);
        }
        $response['project_chart']['label'] = $lastSixMonthsLabelPro;
        $response['project_chart']['data'] = $lastSixMonthsPro;
        // $response['project_chart']['data'] = $lastSixMonthsDataPro;
        return $response;
    }

    public function checkIsGoalReached($projArr)
    {
        $status = '';
        // if (empty($projArr) || empty($projArr['project_donation_type'])) {
        //     return  $status;
        // }
        // if (
        //     in_array(config('constants.donation'), $projArr['project_donation_type']) &&
        //     in_array(config('constants.volunteer'), $projArr['project_donation_type'])
        // ) {
        //     if (
        //         $projArr['total_amount'] >= $projArr['project_amount'] &&
        //         $projArr['totalVolunteer'] >= $projArr['volunteer']
        //     ) {
        //         $status = config('constants.status_goal_reach');
        //     }
        // } elseif (
        //     in_array(config('constants.donation'), $projArr['project_donation_type']) &&
        //     !in_array(config('constants.volunteer'),  $projArr['project_donation_type'])
        // ) {
        //     if ($projArr['total_amount'] >= $projArr['project_amount']) {
        //         $status = config('constants.status_goal_reach');
        //     }
        // } elseif (
        //     !in_array(config('constants.donation'), $projArr['project_donation_type']) &&
        //     in_array(config('constants.volunteer'), $projArr['project_donation_type']))
        // {
        //     if ($projArr['totalVolunteer'] >= $projArr['volunteer']) {
        //         $status = config('constants.status_goal_reach');
        //     }
        // }
        if ($projArr['total_amount'] >= $projArr['project_amount']) {
            $status = config('constants.status_goal_reach');
        }else{
            $status='';
        }
        return $status;
    }

    public function getVolunteerList($projArr)
    {
        if (empty($projArr)) {
            return $dataArr['success'] = false;
        }
        $volunteer = ProjectVolunteer::where('project_id', $projArr['project_id']);

        $volunteer = $volunteer->orderBy('created_at', 'DESC');
        $volunteer = $volunteer->paginate(env('PAGINATION_COUNT'));
        $response = [];
        if (!empty($volunteer)) {
            $i = 0;
            $page = $projArr['page'];
            foreach ($volunteer as $k => $item) {
                $response[$i]['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                $response[$i]['id'] = $item->encrypt_id;
                $response[$i]['user_name'] = $item->user_name;
                $response[$i]['comment'] = $item->comment;
                $response[$i]['email'] = $item->email;
                $response[$i]['sponser_type'] = $item->sponsor_type;
                $response[$i]['apply_date'] = $item->apply_date;
                $i++;
            }
        }
        $paginateData = $volunteer->toArray();
        $pagination =  pagination($paginateData);
        $dataArr = [
            'response' => $response,
            'pagination' => $pagination,
            'success' => true
        ];
        if ($volunteer->count() == 0) {
            $dataArr['success'] = false;
        }
        return $dataArr;
    }
}
