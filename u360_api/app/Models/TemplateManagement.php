<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateManagement extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'templates_management';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'template',
        'template_type',
        'subject',
        'status',
        'created_by',
        'updated_by'
    ];
}
