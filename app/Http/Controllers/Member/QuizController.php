<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QuizMemberAnswer;
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

            $result=0;

            $Question=Question::where("quiz_id",$data['quizId'])->get();


            $times=0;
            $checkTimes=QuizMemberAnswer::where('member_id',$member->id)
            ->where('quiz_id',$data['quizId'])->orderBy('id','desc')->first();

            if(empty($checkTimes)){
                $times=1;

            }else{

                $times=$checkTimes->times+1;
            }


            foreach ($data['answers'] as $item) {
                $string = is_array($item['answer']) ? implode(',', $item['answer']) : strval($item['answer']);

                $quizMemberAnswerId = DB::table('quiz_member_answer')->insertGetId([
                    'member_id' => $member->id,
                    'quiz_id' => $data['quizId'] ?? '',
                    'question_id' => $item['question_id'] ?? '',
                    'user_answers' => $string ?? '',
                    'times'=>$times
                ]);


                // Lấy danh sách câu trả lời đúng theo question_id
                $answers = Answer::where('question_id', $item['question_id'])
                    ->where('correct_answer', 1)
                    ->pluck('question_id', 'id')
                    ->toArray();

                // Lấy bản ghi vừa chèn từ model
                $quizMemberAnswer = QuizMemberAnswer::find($quizMemberAnswerId);


                // Kiểm tra điều kiện
                if ($quizMemberAnswer) {
                    $userAnswerIds = explode(',', $quizMemberAnswer->user_answers);
                    $isValid = true;


                    foreach ($userAnswerIds as $id) {
                        if (!isset($answers[$id]) || $answers[$id] == 0) {
                            $isValid = false;
                            break;
                        }
                    }

                    if ($isValid) {
                        $result++; // Nếu tất cả ID trong user_answers đều có correct_answer = 1 thì cộng thêm 1
                    }


                }
            }


            return response()->json([
                'status'=>true,
                'total'=>count($Question),
                'result'=>$result,
                'times'=>$times
            ]);



        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
