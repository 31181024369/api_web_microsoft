<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'gift_id';

    protected $fillable = [
        'title',
        'description',
        'reward_point',
        'picture',
        'display',
    ];
}
