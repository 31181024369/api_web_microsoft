<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;
class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $query=Quiz::query();
            if(!empty($request->input('data'))&& $request->input('data') !== 'null'&& $request->input('data') !== 'undefined'){
                $query=$query->where("name", 'like', '%' . $request->input('data') . '%');
            }
            $query= $query->orderBy('id','desc')->get();
            return response()->json([
                'status'=>true,
                'data'=>$query
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
            $Quiz=new Quiz();
            $Quiz->name=$request->name;
            $Quiz->description=$request->description??'';
            $Quiz->diffculty=$request->diffculty??'';
            $Quiz->save();
            foreach($request->questions as $questions){
                $questionId =DB::table('quiz_question')->insertGetId([
                    'quiz_id' => $Quiz->id,
                    'description' =>  $questions->question_text,
                    'image'=>$questions->image??'',
                ]);
                foreach($questions->answers as $answers){
                    //quiz_answer
                    DB::table('quiz_answer')->insert([
                        'question_id' => $questionId,
                        'description' =>  $questions->question_text,
                        'correct_answer'=>$questions->is_correct??'',
                    ]);

                }
            }

            return response()->json([
                'status'=>true,
                'data'=>$Quiz
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
            $Quiz=Quiz::with('Question.Answer')->where('id',$id)->first();
            return response()->json([
                'status'=>true
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
            $Quiz=Quiz::where('id',$id)->first();
            $question =DB::table('quiz_question')->where('quiz_id',$Quiz->id)->first();
            $answerId=DB::table('quiz_answer')->where('question_id',$question->id)->first();
            if($answerId){
                $answerId->delete();
            }
            if($question){
                $question->delete();
            }
            foreach($request->questions as $questions){
                $questionId =DB::table('quiz_question')->insertGetId([
                    'quiz_id' => $Quiz->id,
                    'description' =>  $questions->question_text,
                    'image'=>$questions->image??'',
                ]);
                foreach($questions->answers as $answers){
                    //quiz_answer
                    DB::table('quiz_answer')->insert([
                        'question_id' => $questionId,
                        'description' =>  $questions->question_text,
                        'correct_answer'=>$questions->is_correct??'',
                    ]);

                }
            }
            return response()->json([
                'status'=>true,
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $Quiz=Quiz::where('id',$id)->first();
            $question =DB::table('quiz_question')->where('quiz_id',$Quiz->id)->first();
            $answerId=DB::table('quiz_answer')->where('question_id',$question->id)->first();
            if($Quiz){
                $Quiz->delete();
                if($answerId){
                    $answerId->delete();
                }
                if($question){
                    $question->delete();
                }

            }
            return response()->json([
                'status'=>true
            ]);
        }catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
