<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasFactory;

    protected $table = 'project_categories';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'category_id',
        'created_by',
        'updated_by'
    ];

    public function hasProject()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    public function hasCategories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
