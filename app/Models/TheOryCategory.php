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
        'short_description',
        'friendly_url',
        'meta_keywords',
        'meta_description',
        'picture',
        'display',
        'parentid',
    ];

    public function parent()
    {
        return $this->belongsTo(TheOryCategory::class, 'parentid', 'cat_id');
    }

    public function children()
    {
        return $this->hasMany(TheOryCategory::class, 'parentid', 'cat_id');
    }

    public function theories()
    {
        return $this->hasMany(TheOry::class, 'cat_id', 'cat_id');
    }
}
