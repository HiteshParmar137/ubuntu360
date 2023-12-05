<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectReview extends Model
{
    use HasFactory;
    
    protected $table = 'project_review';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'project_id',
        'rating',
        'comment'
    ];

    public function hasProject()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
