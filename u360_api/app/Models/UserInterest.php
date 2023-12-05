<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    use HasFactory;

    protected $table = 'user_interest';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'created_by',
        'updated_by'
    ];
}
