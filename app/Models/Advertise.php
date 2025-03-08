<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertise extends Model
{
    protected $table = 'advertise';
    protected $primaryKey = 'id';
    use  HasFactory;


    protected $fillable = [
        'title',
        'picture',
        'id_pos',
        'width',
        'height',
        'link',
        'description',
        'display',
    ];
    public function Adpos()
    {
        return $this->belongsTo(Adpos::class, 'id_pos','id');
    }
}
