<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUserDetail extends Model
{
    use HasFactory;

    public $timestamps = true;
	
	protected $table = 'app_user_details';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
	    'user_id',
	    'dob',
		'location',
		'phone_code',
		'contact_number',
		'corporation_name',
		'industry',
		'other_industry',
		'city',
		'country',
		'contact_name',
		'position',
		'sdg_id',
		'twitter',
		'facebook',
		'linkedin',
		'instagram',
		'snapchat',
		'tiktok'
	];

	public function hasUser()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
	public function hasIndustry(){
		return $this->hasOne(Industry::class, 'industry', 'id');
	}
}
