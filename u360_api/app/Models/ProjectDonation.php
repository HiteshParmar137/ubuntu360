<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class ProjectDonation extends Model
{
    use HasFactory;

    protected $table = 'project_donations';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'user_id',
        'comment',
        'donation_amount',
        'donation_type',
        'month_end_date',
        'tips_amount',
        'email',
        'name',
        'status',
        'is_recurring_stop'
    ];

    public function hasProject()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function getTotalDonationAttribute()
    {
        return Transaction::where('project_id', $this->project_id)->where('user_id', $this->user_id)->sum('amount');
    }

    public function hasUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function hasTransactions()
    {
        return $this->hasMany(Transaction::class, 'donation_id', 'id');
    }
}
