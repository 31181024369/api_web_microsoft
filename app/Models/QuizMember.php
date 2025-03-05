<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizMember extends Model
{
    protected $table = 'quiz_member';
    protected $primaryKey = 'id';
    protected $fillable = [
       'member_id','quiz_id','is_finish','time_start','time_end','time'
    ];
}
