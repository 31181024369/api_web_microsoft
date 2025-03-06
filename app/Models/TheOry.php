<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheOry extends Model
{
    use HasFactory;

    protected $table = 'theory';

    protected $primaryKey = 'theory_id';

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'friendly_url',
        'meta_keywords',
        'meta_description',
        'picture',
        'display',
        'cat_id',
    ];

    public function category()
    {
        return $this->belongsTo(TheOryCategory::class, 'cat_id', 'cat_id');
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class, 'theory_id', 'theory_id');
    }
}
