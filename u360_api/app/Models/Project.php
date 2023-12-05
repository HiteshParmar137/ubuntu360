<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'project_type',
        'project_donation_type',
        'category_id',
        'title',
        'description',
        'default_image',
        'amount',
        'volunteer',
        'city',
        'country',
        'sdg_id',
        'sdg_ids',
        'status',
        'is_donation_reached',
        'project_url',
    ];

    public function hasProjectCategories()
    {
        return $this->hasMany(ProjectCategory::class, 'project_id', 'id');
    }
    
    public function hasProjectCategory()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    public function hasUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function hasProjectDocuments()
    {
        return $this->hasMany(ProjectDocument::class, 'project_id', 'id');
    }

    public function hasFollows()
    {
        return $this->hasMany(ProjectFollow::class, 'project_id', 'id');
    }

    // public function hasLikes()
    // {
    //     return $this->hasMany(ProjectLike::class, 'project_id', 'id');
    // }

    public function hasTransactions()
    {
        return $this->hasMany(Transaction::class, 'project_id', 'id');
    }

    // public function getTotalLikesAttribute()
    // {
    //     return $this->hasMany(ProjectLike::class)->whereProjectId($this->id)->count();
    // }

    public function getTotalFollowsAttribute()
    {
        return $this->hasMany(ProjectFollow::class)->whereProjectId($this->id)->count();
    }

    public function getTotalCommentsAttribute()
    {
        return $this->hasMany(ProjectComment::class)->whereProjectId($this->id)->count();
    }

    public function hasDonations()
    {
        return $this->hasMany(ProjectDonation::class, 'project_id', 'id');
    }
    public function hasVolunteer()
    {
        return $this->hasMany(ProjectVolunteer::class, 'project_id', 'id');
    }

    public function hasRejecReason()
    {
        return $this->hasMany(ProjectRejectReason::class, 'project_id', 'id');
    }
    public function hasCountry()
    {
        return $this->hasOne(Country::class, 'id', 'country');
    }
}
