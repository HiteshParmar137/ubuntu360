<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'created_by',
        'updated_by'
    ];

    public function hasProjectCategories()
    {
        return $this->hasMany(ProjectCategory::class, 'category_id', 'id');
    }
}