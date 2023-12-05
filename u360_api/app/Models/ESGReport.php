<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESGReport extends Model
{
    use HasFactory;

    protected $table = 'esg_reports';

    public $timestamps = true;

    protected $fillable = [
        'email',
    ];
}
