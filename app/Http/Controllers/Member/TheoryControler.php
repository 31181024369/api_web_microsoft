<?php

namespace App\Http\Controllers\Member;

use App\Models\TheOryCategory;
use App\Models\TheOry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizMember;

class TheoryControler extends Controller
{
    public function index()
    {
        try {
            $theOryCategory = TheOryCategory::with(['theories.quizzes.questions'])->get();
            $response = [];
            $member_id = Auth::guard('member')->user()?->id;
            foreach ($theOryCategory as $key => $item) {
                $theories = $item->theories->map(function ($theory) use ($item, $member_id) {
                    $quiz = $theory->quizzes->first(function ($quiz) use ($item, $theory) {
                        return $quiz->cat_id == $item->cat_id && $quiz->theory_id == $theory->theory_id;
                    });

                    $theoryData = [
                        'id' => $theory->theory_id,
                        'title' => $theory->title,
                    ];

                    if ($quiz) {
                        $hasAttempted = QuizMember::where('member_id', $member_id)
                            ->where('quiz_id', $quiz->id)
                            ->exists();

                        $theoryData['quiz'] = [
                            'id' => $quiz->id,
                            'name' => $quiz->name,
                            'friendly_url' => $quiz->friendly_url,
                            'time' => $quiz->time,
                            'pointAward' => $quiz->pointAward,
                            'question_count' => $quiz->questions->count(),
                            'has_attempted' => $hasAttempted
                        ];
                    }

                    return $theoryData;
                })->filter(function ($theory) {
                    return isset($theory['quiz']);
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
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
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
