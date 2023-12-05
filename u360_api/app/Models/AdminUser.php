<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\UsersGroup;
use App\Notifications\MailResetPasswordNotification;

class AdminUser extends Authenticatable implements JWTSubject
{
	// use HasFactory;
	use HasApiTokens, HasFactory, Notifiable;
	use SoftDeletes;

	protected $table = 'admin_users';

	public $timestamps = true;

	protected $fillable = [
		'name', 'email', 'status', 'password', 'image','reset_password_token','user_type','user_group_id'
	];

	// public function userGroup()
	// {
	// 	return $this->hasOne(UsersGroup::class, 'id', 'user_group_id');
	// }

	public function getJWTIdentifier()
	{
		return $this->getKey();
	}

	public function getJWTCustomClaims()
	{
		return [];
	}
	public function usersGroup()
	{
		return $this->belongsTo(UsersGroup::class, 'user_group_id', 'id');
	}
}
