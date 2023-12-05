<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectVolunteer extends Model
{
    use HasFactory;

    protected $table = 'project_volunteers';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'project_id',
        'volunteer_type',
        'comment',
        'email'
    ];
    public function hasUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUserNameAttribute()
    {
        return $this->hasUser->name;
    }
    public function getEncryptIdAttribute()
    {
        return encrypt($this->id);
    }
    public function getSponsorTypeAttribute()
    {
        if($this->volunteer_type == 1){
            $type= 'In person Volunteering';
        }elseif($this->volunteer_type == 2){
            $type= 'Online Volunteering';
        }elseif($this->volunteer_type == 3){
            $type= 'Consulting';
        }else{
            $type ='-';
        }
        return $type;
    }
    public function getApplyDateAttribute()
    {
        return dateformat($this->created_at);
    }
}
