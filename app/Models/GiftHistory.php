<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftHistory extends Model
{
    protected $table = 'gift_history';
    //protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'member_id',
        'gift_id',
        'points_used',
        'remaining_points',
        'redeemed_at',
        'is_confirmed',
        'confirmed_at',
        'cityAddress',
        'districtAddress',
        'wardAddress',
        'streetAddress',
        'numberPhone'
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'is_confirmed' => 'boolean',
        'confirmed_at' => 'datetime'
    ];

    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class, 'gift_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
