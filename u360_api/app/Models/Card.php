<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $table = 'cards';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'customer_id',
        'card_id',
    ];

    public function hasProjectCategories()
    {
        return $this->hasMany(ProjectCategory::class, 'category_id', 'id');
    }
}
