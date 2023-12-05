<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFollow extends Model
{
    use HasFactory;

    protected $table = 'project_follows';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'project_id',
    ];

    public function hasProject()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
