<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public function submitQuiz(Request $request){
        try{
            $member=Auth::guard('member')->user();
            $data=$request->all();
            foreach($data['answers'] as $item){
                $string='';
                if (is_array($item['answer'])) {
                    $string = implode(',', $item['answer']);
                } else {
                    $string = strval($item['answer']);
                }

                $quizMemberAnswer=DB::table('quiz_member_answer')->insert([
                    'member_id' => $member->id,
                    'quiz_id'=>$data['quizId']??'',
                    'question_id' =>  $item['question_id']??'',
                    'user_answers'=> $string??'',
                ]);
                Answer::where('question_id',$item['question_id'])->where('correct_answer',1);
            }


            return response()->json([
                'status'=>true
            ]);



        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
