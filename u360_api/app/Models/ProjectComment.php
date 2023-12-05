<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectComment extends Model
{
	use HasFactory;

	protected $table = 'project_comments';

	public $timestamps = true;

	protected $fillable = [
		'user_id',
		'project_id',
		'comment',
		'rating',
	];

	public function hasUser()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function hasProject()
	{
		return $this->belongsTo(Project::class, 'project_id', 'id');
	}
}
