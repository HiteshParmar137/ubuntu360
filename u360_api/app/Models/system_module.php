<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class system_module extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'system_modules';
    protected $fillable = [
        'module_name', 'action', 'slug'
    ];
}
