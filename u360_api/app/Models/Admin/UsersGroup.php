<?php

namespace App\Models\Admin;

use App\Models\AdminUser;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UsersGroup extends Authenticatable
{
    use Notifiable;
    protected $table = 'user_groups';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_name', 'description', 'status', 'permissions', 'year_group'
    ];

    public function hasAdminUsers()
    {
        return $this->hasMany(AdminUser::class, 'user_group_id', 'id');
    }
}
