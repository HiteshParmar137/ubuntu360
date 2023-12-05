<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDocument extends Model
{
    use HasFactory;

    protected $table = 'project_documents';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'document_type',
        'document_name',
        'document_original_name',
        'created_by',
        'updated_by',
    ];

    public function hasProject()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
