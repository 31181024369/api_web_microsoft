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
            $theOryCategory = TheOryCategory::with(['theories', 'quizzes.questions'])->get();
            $response = [
                'status' => true,
                'list' => $theOryCategory->map(function ($item) {
                    return [
                        'id' => $item->cat_id,
                        'title' => $item->title,
                        'theories' => $item->theories->map(function ($theory) {
                            return [
                                'id' => $theory->theory_id,
                                'title' => $theory->title,
                            ];
                        }),
                        'quizzes' => $item->quizzes->map(function ($quiz) {
                            return [
                                'id' => $quiz->id,
                                'title' => $quiz->title,
                                'time' => $quiz->time,
                                'pointAward' => $quiz->pointAward,
                                'question_count' => $quiz->questions->count(),
                            ];
                        }),
                    ];
                })
            ];
            return response()->json($response, 200);
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
