<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSponsor extends Model
{
    use HasFactory;

    protected $table = 'project_sponsors';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'user_id',
        'donation_id',
    ];

    public function hasProject()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function hasUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
