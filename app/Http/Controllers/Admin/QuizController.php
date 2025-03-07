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
        try {
            $query = Quiz::query();
            if (!empty($request->input('data')) && $request->input('data') !== 'null' && $request->input('data') !== 'undefined') {
                $query = $query->where("name", 'like', '%' . $request->input('data') . '%');
            }
            if (!empty($request->input('cat_id')) && $request->input('cat_id') !== 'null' && $request->input('cat_id') !== 'undefined') {
                $query = $query->where("cat_id", $request->input('cat_id'));
            }


            $query = $query->orderBy('id', 'desc')->paginate(10);
            return response()->json([
                'status' => true,
                'data' => $query
            ]);
        } catch (\Exception $error) {

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
        try {
            $disPath = public_path();
            $Quiz = new Quiz();

            $filePath = '';
            if ($request->selectedFile != null) {
                $DIR = $disPath . '\uploads\quiz';
                $httpPost = file_get_contents('php://input');
                $file_chunks = explode(';base64,', $request->selectedFile[0]);
                $fileType = explode('image/', $file_chunks[0]);
                $image_type = $fileType[0];
                //return response()->json( $file_chunks );
                $base64Img = base64_decode($file_chunks[1]);
                $data = iconv('latin5', 'utf-8', $base64Img);
                $name = uniqid();
                $file = $DIR . '\\' . $name . '.png';
                $filePath = 'quiz/' . $name . '.png';
                file_put_contents($file,  $base64Img);
            }
            $Quiz->name = $request->title;
            // $Quiz->description=$request->description??'';
            $Quiz->pointAward = $request->pointAward ?? '';
            $Quiz->picture = $filePath;
            $Quiz->cat_id = $request->cat_id;
            $Quiz->theory_id = $request->theory_id;
            $Quiz->time = $request->duration ?? 0;
            $Quiz->display = $request->visible ?? 0;
            $Quiz->friendly_url = $request->friendlyUrl;
            $Quiz->friendly_title = $request->pageTitle;
            $Quiz->metakey = $request->metaKeyword;
            $Quiz->metadesc = $request->metaDesc;
            $Quiz->save();
            foreach ($request->questions as $questions) {

                $questionId = DB::table('quiz_question')->insertGetId([
                    'quiz_id' => $Quiz->id,
                    'description' =>  $questions['question_text'],
                    // 'image'=>$questions->image??'',
                ]);

                foreach ($questions['answers'] as $answers) {
                    //quiz_answer
                    DB::table('quiz_answer')->insert([
                        'question_id' => $questionId,
                        'letter' => $answers['option_letter'] ?? '',
                        'description' =>  $answers['option_text'] ?? '',
                        'correct_answer' => $answers['is_correct'] ?? '',
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'data' => $Quiz
            ]);
        } catch (\Exception $error) {

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
        try {
            $Quiz = Quiz::with('Question.Answer')->where('id', $id)->first();

            return response()->json([
                'status' => true,
                'data' => $Quiz
            ]);
        } catch (\Exception $error) {

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
        try {
            $Quiz = Quiz::where('id', $id)->first();
            if (!$Quiz) {
                return response()->json([
                    'status' => false,
                    'message' => 'Quiz not found'
                ], 404);
            }

            $filePath = '';
            if ($request->selectedFile != null && $request->selectedFile != $Quiz->picture) {

                $DIR = $disPath . '\uploads\quiz';
                $httpPost = file_get_contents('php://input');
                $file_chunks = explode(';base64,', $request->selectedFile[0]);
                $fileType = explode('image/', $file_chunks[0]);
                $image_type = $fileType[0];

                //return response()->json( $file_chunks );
                $base64Img = base64_decode($file_chunks[1]);
                $data = iconv('latin5', 'utf-8', $base64Img);
                $name = uniqid();
                $file = $DIR . '\\' . $name . '.png';
                $filePath = 'quiz/' . $name . '.png';

                file_put_contents($file,  $base64Img);
            } else {
                $filePath = $Quiz->picture;
            }

            $Quiz->name = $request->title;
            $Quiz->picture = $filePath;
            $Quiz->cat_id = $request->cat_id;
            $Quiz->theory_id = $request->theory_id;
            $Quiz->pointAward = $request->pointAward ?? '';
            $Quiz->time = $request->duration ?? 0;
            $Quiz->display = $request->visible ?? 0;
            $Quiz->friendly_url = $request->friendlyUrl;
            $Quiz->friendly_title = $request->pageTitle;
            $Quiz->metakey = $request->metaKeyword;
            $Quiz->metadesc = $request->metaDesc;
            $Quiz->save();


            $question = DB::table('quiz_question')->where('quiz_id', $Quiz->id)->first();
            $answerId = DB::table('quiz_answer')->where('question_id', $question->id)->first();
            if ($answerId) {
                DB::table('quiz_answer')->where('question_id', $question->id)->delete();
            }
            if ($question) {
                DB::table('quiz_question')->where('quiz_id', $Quiz->id)->delete();
            }
            foreach ($request->questions as $questions) {
                $questionId = DB::table('quiz_question')->insertGetId([
                    'quiz_id' => $Quiz->id,
                    'description' =>  $questions['question_text'],
                    // 'image'=>$questions->image??'',
                ]);
                foreach ($questions['answers'] as $answers) {
                    //quiz_answer
                    DB::table('quiz_answer')->insert([
                        'question_id' => $questionId,
                        'letter' => $answers['option_letter'] ?? '',
                        'description' =>  $answers['option_text'] ?? '',
                        'correct_answer' => $answers['is_correct'] ?? '',
                    ]);
                }
            }
            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $error) {

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
        try {
            $Quiz = Quiz::where('id', $id)->first();
            if (!$Quiz) {
                return response()->json([
                    'status' => false,
                    'message' => 'Quiz not found'
                ], 404);
            }
            $question = DB::table('quiz_question')->where('quiz_id', $Quiz->id)->first();
            $answerId = DB::table('quiz_answer')->where('question_id', $question->id)->first();
            if ($Quiz) {

                if ($answerId) {
                    DB::table('quiz_answer')->where('question_id', $question->id)->delete();
                }
                if ($question) {
                    DB::table('quiz_question')->where('quiz_id', $Quiz->id)->delete();
                }
                $Quiz->delete();
            }
            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
