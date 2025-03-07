<?php

namespace App\Http\Controllers\Member;

use App\Models\TheOryCategory;
use App\Models\TheOry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class TheoryControler extends Controller
{
    public function index()
    {
        try {
            $theOryCategory = TheOryCategory::with(['theories.quizzes.questions'])->get();
            $response = [];

            foreach ($theOryCategory as $key => $item) {
                $theories = $item->theories->map(function ($theory) use ($item) {
                    $quizzes = $theory->quizzes->filter(function ($quiz) use ($item, $theory) {
                        return $quiz->cat_id == $item->cat_id && $quiz->theory_id == $theory->theory_id;
                    })->take(10)->map(function ($quiz) {
                        return [
                            'id' => $quiz->id,
                            'title' => $quiz->title,
                            'friendly_url' => $quiz->friendly_url,
                            'time' => $quiz->time,
                            'pointAward' => $quiz->pointAward,
                            'question_count' => $quiz->questions->count(),
                        ];
                    });

                    $theoryData = [
                        'id' => $theory->theory_id,
                        'title' => $theory->title,
                    ];

                    if ($quizzes->isNotEmpty()) {
                        $theoryData['quizzes'] = $quizzes;
                    }

                    return $theoryData;
                })->filter(function ($theory) {
                    return isset($theory['quizzes']);
                });

                if ($theories->isNotEmpty()) {
                    $response[] = [
                        'id' => $item->cat_id,
                        'title' => $item->title,
                        'theories' => $theories,
                    ];
                }
            }

            return response()->json(['status' => true, 'list' => $response], 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $response = [
                'status' => 'false',
                'error' => $errorMessage
            ];

            return response()->json($response, 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $friendlyUrl = $request->route('friendly_url');

            $theory = TheOry::where('friendly_url', $friendlyUrl)
                ->with(['quizzes' => function ($query) {
                    $query->select('id', 'theory_id', 'title', 'friendly_url', 'time', 'pointAward')
                        ->with('questions:id,quiz_id');
                }])
                ->select('theory_id', 'title', 'description', 'short_description', 'friendly_url', 'picture')
                ->firstOrFail();

            $response = [
                'status' => true,
                'data' => [
                    'id' => $theory->theory_id,
                    'title' => $theory->title,
                    'description' => $theory->description,
                    'short_description' => $theory->short_description,
                    'friendly_url' => $theory->friendly_url,
                    'picture' => $theory->picture,
                    'quizzes' => $theory->quizzes->map(function ($quiz) {
                        return [
                            'id' => $quiz->id,
                            'title' => $quiz->title,
                            'friendly_url' => $quiz->friendly_url,
                            'time' => $quiz->time,
                            'pointAward' => $quiz->pointAward,
                            'question_count' => $quiz->questions->count()
                        ];
                    })
                ]
            ];

            return response()->json($response, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'error' => 'Không tìm thấy bài học'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
