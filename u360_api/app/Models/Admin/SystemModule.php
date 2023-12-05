<?php

namespace App\Models\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SystemModule extends Authenticatable
{
    use Notifiable;
    protected $table = 'system_module';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module_name', 'slug', 'action'
    ];

}
