<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'quiz';
    protected $primaryKey = 'id';
    protected $fillable = [
        'member_id','quiz_id', 'total_questions', 'total_correct'
    ];
}
