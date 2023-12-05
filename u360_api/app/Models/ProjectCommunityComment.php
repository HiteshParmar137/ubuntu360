<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCommunityComment extends Model
{
    use HasFactory;
    
    protected $table = 'project_community_comments';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'project_id',
        'project_community_id',
        'comment'
    ];
    public function hasUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    // public function hasProject()
    // {
    //     return $this->belongsTo(Project::class, 'project_id', 'id');
    // }
}
