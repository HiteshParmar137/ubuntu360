<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUserInterest extends Model
{
    use HasFactory;

    public $timestamps = true;
	
	protected $table = 'app_user_interest';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	public function hasCategory()
    {
        return $this->belongsTo(Category::class, 'interest_id', 'id');
    }
}
