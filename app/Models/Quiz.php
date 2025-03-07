<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;

class Quiz extends Model
{
    protected $table = 'quiz';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'picture',
        'diffculty',
        'time',
        'display',
        'friendly_url',
        'friendly_title',
        'metakey',
        'metadesc',
        'pointAward',
        'cat_id',
        'theory_id'
    ];
    public function Question()
    {
        return $this->hasMany(Question::class, 'quiz_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id', 'id');
    }

    public function theory()
    {
        return $this->belongsTo(TheOry::class, 'theory_id', 'theory_id');
    }

    public function category()
    {
        return $this->belongsTo(TheOryCategory::class, 'cat_id', 'cat_id');
    }
}
