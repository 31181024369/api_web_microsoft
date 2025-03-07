<?php

namespace App\Http\Controllers\Member;

use App\Models\TheOryCategory;
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
                    })->map(function ($quiz) {
                        return [
                            'id' => $quiz->id,
                            'title' => $quiz->title,
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
                });

                $response[] = [
                    'id' => $item->cat_id,
                    'title' => $item->title,
                    'theories' => $theories->filter(function ($theory) {
                        return isset($theory['quizzes']);
                    })->values(),
                ];
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

    public function create() {}

    public function store(Request $request) {}

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
