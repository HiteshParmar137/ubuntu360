<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sdg extends Model
{
    use HasFactory;
    protected $table = 'sdgs';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'created_by',
        'updated_by'
    ];
}
