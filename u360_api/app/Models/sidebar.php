<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class sidebar extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'sidebars';
    protected $fillable = [
        'name', 'url'
    ];
}
