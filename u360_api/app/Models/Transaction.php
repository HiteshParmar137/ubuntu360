<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'donation_id',
        'stripe_customer_id',
        'transaction_id',
        'amount',
        'type',
        'status',
        'created_by',
        'updated_by',
    ];

    public function hasProject()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function hasUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function hasDonation()
    {
        return $this->belongsTo(ProjectDonation::class, 'donation_id', 'id');
    }
}
