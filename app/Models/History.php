<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Member;
use App\Models\Quiz;
class History extends Model
{
    protected $table = 'history';
    protected $primaryKey = 'id';
    protected $fillable = [
        'member_id','quiz_id', 'total_questions', 'total_correct','times'
    ];
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id','id')->select(['id', 'username']);
    }
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id','id')->select(['id', 'name']);
    }
}
