<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
class Answer extends Model
{
    protected $table = 'quiz_answer';
    protected $primaryKey = 'id';
    protected $fillable = [
        'description', 'correct_answer', 'question_id','letter'
    ];
    public function quiz()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id'); // Đúng thứ tự
    }

}
