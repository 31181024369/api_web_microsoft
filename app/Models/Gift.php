<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $fillable = [
        'title',
        'description',
        'reward_point',
        'picture',
        'display'
    ];

    protected $casts = [
        'display' => 'integer',
        'reward_point' => 'integer'
    ];
}
