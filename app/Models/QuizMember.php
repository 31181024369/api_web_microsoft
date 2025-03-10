<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Member;
use App\Models\Quiz;
use App\Models\History;

class QuizMember extends Model
{
    protected $table = 'quiz_member';
    protected $primaryKey = 'id';
    protected $fillable = [
        'member_id',
        'quiz_id',
        'is_finish',
        'time_start',
        'time_end',
        'times'
    ];
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id')->select(['id', 'username']);;
    }
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id')->select(['id', 'name']);;
    }

    public function quizzes()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function history()
    {
        return $this->hasOne(History::class, 'quiz_id', 'quiz_id')
            ->where('member_id', $this->member_id);
    }

    public function theory()
    {
        return $this->belongsTo(Theory::class, 'theory_id');
    }
}
