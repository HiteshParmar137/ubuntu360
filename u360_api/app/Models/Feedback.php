<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'feedback';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'comment',
        'rating'
    ];
    
    public function hasUser()
    {
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}
