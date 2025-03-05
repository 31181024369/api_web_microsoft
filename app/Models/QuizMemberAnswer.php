<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizMemberAnswer extends Model
{
    protected $table = 'quiz_member_answer';
    protected $primaryKey = 'id';
    protected $fillable = [
       'member_id','quiz_id','question_id','user_answers'
    ];
}
