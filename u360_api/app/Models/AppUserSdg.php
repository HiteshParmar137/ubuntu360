<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUserSdg extends Model
{
    use HasFactory;

    public $timestamps = true;
	
	protected $table = 'app_user_sdgs';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */

	public function hasSdgs()
    {
        return $this->belongsTo(Sdg::class, 'sdg_id', 'id');
    }
}
