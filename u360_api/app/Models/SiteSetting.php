<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'site_settings';
    protected $fillable = [
        'key_name',
        'key_value',
        'created_by',
        'updated_by'
    ];
}
