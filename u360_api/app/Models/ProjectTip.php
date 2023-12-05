<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTip extends Model
{
    use HasFactory;

    protected $table = 'project_tips';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'donation_id',
        'user_id',
        'comment',
        'tips_amount',
        'tips_recurring',
    ];
}
