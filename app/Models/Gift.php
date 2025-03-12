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
        'display',
        'quantity',
        'create_at'
    ];

    protected $casts = [
        'display' => 'integer',
        'reward_point' => 'integer',
        'quantity' => 'integer',
    ];
}
