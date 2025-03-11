<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'members';
    protected $primaryKey = 'id';

    protected $hidden = [
        'password',
        'remember_token',
        // 'create_at',
        // 'update_at',
    ];
    protected $fillable = [
        'username',
        'mem_code',
        'email',
        'password',
        'address',
        'company',
        'full_name',
        'provider',
        'avatar',
        'phone',
        'status',
        'm_status',
        'ward',
        'district',
        'city_province',
        'password_token',
        'tax',
        'nameCompany',
        'points',
        'used_points',
        'number_passes'
    ];

    protected $casts = [
        'points' => 'integer',
        'used_points' => 'integer',
    ];
}
