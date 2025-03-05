<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz;
use App\Models\Answer;
class Question extends Model
{
    protected $table = 'quiz_question';
    protected $primaryKey = 'id';
    protected $fillable = [
        'description', 'image', 'quiz_id'
    ];
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id'); // Đúng thứ tự
    }
    public function Answer()
    {
        return $this->hasMany(Answer::class,'question_id','id');
    }
    public function AnswerUser()
    {
        return $this->hasMany(Answer::class, 'question_id', 'id')
                ->select(['id', 'question_id', 'description', 'letter']);
    }

}
