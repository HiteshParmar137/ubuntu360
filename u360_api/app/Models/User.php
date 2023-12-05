<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\MailResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
	use HasApiTokens, HasFactory, Notifiable;
	use SoftDeletes;

	public $timestamps = true;

	protected $table = 'users';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'email',
		'image',
		'status',
		'password',
		'user_type',
		'social_id',
		'email_verify_token',
		'reset_password_token',
		'verify_email_token',
		'is_signup_completed',
		'is_email_verified',
		'about',
		'api_token'
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	// protected $hidden = [
	//     'password',
	//     'remember_token',
	// ];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	public function getJWTIdentifier()
	{
		return $this->getKey();
	}

	public function getJWTCustomClaims()
	{
		return [];
	}

	public function hasUserDetails()
	{
		return $this->hasOne(AppUserDetail::class, 'user_id', 'id');
	}

	public function hasSdgsDetails()
	{
		return $this->hasMany(AppUserSdg::class, 'user_id', 'id');
	}

	public function hasInterestDetails()
	{
		return $this->hasMany(AppUserInterest::class, 'user_id', 'id');
	}

	public function hasFeedback()
	{
		return $this->hasOne(Feedback::class, 'user_id', 'id');
	}

	public static function getUserDetails($user)
	{
		$response = [];
		$response['userDetails']['id'] = $user->id ?? 0;
		$response['userDetails']['name'] = $user->name ?? '';
		$response['userDetails']['email'] = $user->email ?? '';
		$response['userDetails']['about'] = $user->about ?? '';
		$response['userDetails']['status'] = (!empty($user->status) && $user->status == '1') ? 'Active' : 'Inactive';
		$response['userDetails']['dob'] = (!empty($user->hasUserDetails->dob))
			? dateformat($user->hasUserDetails->dob) : '';
		$response['userDetails']['location'] = $user->hasUserDetails->location ?? '';
		$response['userDetails']['contact_number'] = $user->hasUserDetails->contact_number ?? '';
		$response['userDetails']['phone_code'] = $user->hasUserDetails->phone_code ?? '';
		$response['userDetails']['sponsor_type'] = $user->user_type ?? '';
		$response['userDetails']['corporation_name'] = $user->hasUserDetails->corporation_name ?? '';
		$response['userDetails']['industry_id'] = $user->hasUserDetails->industry ?? '';
		$response['userDetails']['industry'] = '';
		if (!empty($user->hasUserDetails->industry)) {
			$industryName = Industry::select('name')->where('id', $user->hasUserDetails->industry)
				->get()->toArray();
			if (!empty($industryName) && !empty($industryName[0])) {
				$response['userDetails']['industry'] = $industryName[0]['name'];
			}
		}
		if(!empty($user->hasUserDetails->other_industry)){
			$response['userDetails']['industry'] = 'other';
			$response['userDetails']['other_industry'] = $user->hasUserDetails->other_industry;
		}else{
			$response['userDetails']['other_industry'] = '';
		}
		$response['userDetails']['city'] = $user->hasUserDetails->city ?? '';
		$response['userDetails']['country'] = $user->hasUserDetails->country ?? '';
		$response['userDetails']['contact_name'] = $user->hasUserDetails->contact_name ?? '';
		$response['userDetails']['position'] = $user->hasUserDetails->position ?? '';
		$response['userDetails']['sdg_id'] = $user->hasUserDetails->sdg_id ?? 0;
		$response['userDetails']['twitter'] = $user->hasUserDetails->twitter ?? 0;
		$response['userDetails']['facebook'] = $user->hasUserDetails->facebook ?? 0;
		$response['userDetails']['linkedin'] = $user->hasUserDetails->linkedin ?? 0;
		$response['userDetails']['instagram'] = $user->hasUserDetails->instagram ?? 0;
		$response['userDetails']['snapchat'] = $user->hasUserDetails->snapchat ?? 0;
		$response['userDetails']['tiktok'] = $user->hasUserDetails->tiktok ?? 0;
		$response['userDetails']['sdg_ids'] = $user->hasSdgsDetails ?? [];
		$socialType = ['1' => 'Not at all', '2' => "Rarely", '3' => "Regular"];
		$response['userDetails']['twitter_value'] = !empty($user->hasUserDetails->twitter) ?
			$socialType[$user->hasUserDetails->twitter] : 'Not at all';
		$response['userDetails']['facebook_value'] = !empty($user->hasUserDetails->facebook) ?
			$socialType[$user->hasUserDetails->facebook] : 'Not at all';
		$response['userDetails']['linkedin_value'] = !empty($user->hasUserDetails->linkedin) ?
			$socialType[$user->hasUserDetails->linkedin] : 'Not at all';

		$response['userDetails']['instagram_value'] =
			!empty($user->hasUserDetails->instagram) ?
			$socialType[$user->hasUserDetails->instagram] : 'Not at all';
		$response['userDetails']['snapchat_value'] =
			!empty($user->hasUserDetails->snapchat) ?
			$socialType[$user->hasUserDetails->snapchat] : 'Not at all';
		$response['userDetails']['tiktok_value'] =
			!empty($user->hasUserDetails->tiktok) ?
			$socialType[$user->hasUserDetails->tiktok] : 'Not at all';
		$sdgNameString = '';
		if (!empty($user->hasSdgsDetails)) {
			foreach ($user->hasSdgsDetails as $sdgName) {
				$sdgNameString .= $sdgName->hasSdgs->name . ', ';
			}
			$sdgNameString = rtrim($sdgNameString, ', ');
		}
		$categoryNameString = '';
		if (!empty($user->hasInterestDetails)) {
			foreach ($user->hasInterestDetails as $categoryName) {
				$categoryNameString .= $categoryName->hasCategory->name . ', ';
			}
			$categoryNameString = rtrim($categoryNameString, ', ');
		}
		$response['userDetails']['sdg_name'] = $sdgNameString;
		$response['userDetails']['category_name'] = $categoryNameString;
		$response['userDetails']['interest_ids'] = $user->hasInterestDetails ?? [];
		return $response;
	}
	public function hasProjects()
	{
		return $this->hasMany(Project::class, 'user_id', 'id');
	}
	public function hasDonations()
	{
		return $this->hasMany(ProjectDonation::class, 'user_id', 'id');
	}
	public function hasProjectFollows()
	{
		return $this->hasMany(ProjectFollow::class, 'user_id', 'id');
	}
}
