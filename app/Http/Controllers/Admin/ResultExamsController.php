<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QuizMemberAnswer;
use App\Models\QuizMember;
use App\Models\History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ResultExamsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $catId = $request->cat_id;
            $query=QuizMember::with(['member', 'quiz']);
            if($catId){
                $query->whereHas('quiz', function ($query) use ($catId) {
                    $query->where('cat_id', $catId);
                });
            }
            $QuizMember= $query->orderBy('id','desc')->paginate(10);
            return response()->json([
                'status'=>true,
                'data'=> $QuizMember
            ]);

        }catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

        }catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try{
            $QuizMember=QuizMember::where('id',$id)->first();

            $History=History::with('member','quiz')->where('member_id',$QuizMember->member_id)
            ->where('quiz_id',$QuizMember->quiz_id)->where('times',$QuizMember->times)->first();
            $data=[
                'member'=>$History->member,
                'quiz'=>$History->quiz,
                'totalQuestions'=>$History->total_questions,
                'totalCorrect'=>$History->total_correct,
                'times'=>$History->times,
                'isFinish'=>$QuizMember->is_finish,
                'time_start'=>$QuizMember->time_start,
                'time_end'=>$QuizMember->time_end,
            ];
            return response()->json([
                'status'=>true,
                'data'=>$data
            ]);



        }catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{

        }catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{

        }catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
