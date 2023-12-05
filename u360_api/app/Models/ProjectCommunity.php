<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCommunity extends Model
{
	use HasFactory;

	protected $table = 'project_community';

	public $timestamps = true;

	protected $fillable = [
		'user_id',
		'project_id',
		'comment',
		'type',
	];

	public function hasUser()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
	public function hasProject()
	{
		return $this->belongsTo(Project::class, 'project_id', 'id');
	}
	public function hasDonation()
	{
		return $this->belongsTo(ProjectDonation::class, 'donation_id', 'id');
	}
	public function hasVolunteer()
	{
		return $this->belongsTo(ProjectVolunteer::class, 'volunteer_id', 'id');
	}
	public function hasLikes()
	{
		return $this->hasMany(ProjectCommunityLike::class, 'project_community_id', 'id');
	}
	public function hasComments()
	{
		return $this->hasMany(ProjectCommunityComment::class, 'project_community_id', 'id')->orderBy('created_at', 'desc');
	}
}
