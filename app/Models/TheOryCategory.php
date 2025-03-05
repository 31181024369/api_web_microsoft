<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheOryCategory extends Model
{
    use HasFactory;

    protected $table = 'theory_category';

    protected $primaryKey = 'cat_id';

    protected $fillable = [
        'title',
        'description',
        'friendly_url',
        'parentid',
        'display',
    ];
}
