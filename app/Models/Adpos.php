<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adpos extends Model
{
    protected $table = 'ad_pos';
    protected $primaryKey = 'id_pos';
    use  HasFactory;

    protected $fillable = [
        'name',
        'title',
        'width',
        'height',
        'description',
        'display',
    ];

    public function advertise()
    {
        return $this->hasMany(Advertise::class,'pos','id_pos');
    }
}
