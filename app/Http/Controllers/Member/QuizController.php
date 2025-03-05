<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
class QuizController extends Controller
{
    public function showQuiz(Request $request){
        try{
            $Quiz=Quiz::where('display',1)->orderBy('id','desc')->paginate(10);
            return response()->json([
                'status'=>true,
                'data'=>$Quiz
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function showDetailQuiz($slug){
        try{
            $Quiz=Quiz::with('Question.AnswerUser')->where('friendly_url',$slug)->first();
            return response()->json([
                'status'=>true,
                'data'=>$Quiz
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
