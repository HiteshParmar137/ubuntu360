<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsManagement extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'cms_management';
    protected $fillable = [
        'name',
        'slug',
        'content',
        'status',
        'created_by',
        'updated_by'
    ];
}
