<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminModule extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'admin_modules';
    protected $fillable = [
        'module_name',
        'slug',
        'action',
        'created_by',
        'updated_by',
    ];

    public function hasChild()
    {
        return $this->hasMany(AdminModule::class, 'parent_id', 'id');
    }
}
