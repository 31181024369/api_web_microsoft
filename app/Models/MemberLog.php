<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'friendly_url',
        'count',
        'member_id',
        'module',
        'action',
        'ip_address',
        'created_at',
        'updated_at',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
