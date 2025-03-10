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
                        'friendly_url' => $theory->friendly_url,
                        'picture' => $theory->picture,
                        'short_description' => $theory->short_description,
                        'create_at' => $theory->created_at
                    ];

                    if ($quiz) {
                        $hasAttempted = QuizMember::where('member_id', $member_id)
                            ->where('quiz_id', $quiz->id)
                            ->exists();

                        $is_finish = QuizMember::where('member_id', $member_id)
                            ->where('is_finish', 1)
                            ->exists();

                        $theoryData['quiz'] = [
                            'id' => $quiz->id,
                            'name' => $quiz->name,
                            'friendly_url' => $quiz->friendly_url,
                            'time' => $quiz->time,
                            'pointAward' => $quiz->pointAward,
                            'question_count' => $quiz->questions->count(),
                            'has_attempted' => $hasAttempted,
                            'is_finish' => $is_finish
                        ];
                    }

                    return $theoryData;
                })->filter(function ($theory) {
                    return isset($theory['quiz']);
                })->values();

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

    public function shows(Request $request)
    {
        try {
            $path = $request->path();
            $segments = explode('/', $path);
            $friendlyUrl = end($segments);

            $theory = TheOry::where('friendly_url', $friendlyUrl)
                ->with(['quizzes' => function ($query) {
                    $query->select('id', 'theory_id', 'friendly_url', 'name')
                        ->with('questions:id,quiz_id');
                }])
                ->select('theory_id', 'title', 'description', 'short_description', 'friendly_url', 'picture', 'cat_id')
                ->firstOrFail();

            $quiz = $theory->quizzes->first();

            $response = [
                'status' => true,
                'data' => [
                    'theory' => [
                        'id' => $theory->theory_id,
                        'title' => $theory->title,
                        'description' => $theory->description,
                        'short_description' => $theory->short_description,
                        'friendly_url' => $theory->friendly_url,
                        'picture' => $theory->picture,
                        'cat_id' => $theory->cat_id,
                    ],
                    'quiz' => $quiz ? [
                        'id' => $quiz->id,
                        'name' => $quiz->name,
                        'friendly_url' => $quiz->friendly_url,
                        'question_count' => $quiz->questions->count()
                    ] : null
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

    public function take5theory()
    {
        try {
            $member_id = Auth::guard('member')->id();

            $theories = Theory::whereHas('quizzes')
                ->with(['quizzes' => function ($query) {
                    $query->select('id', 'theory_id', 'name', 'friendly_url', 'time', 'pointAward')
                        ->with('questions:id,quiz_id');
                }])
                ->select('theory_id', 'title', 'short_description', 'friendly_url', 'picture')
                ->orderBy('theory_id', 'desc')
                ->limit(5)
                ->get();

            $data = $theories->map(function ($theory) use ($member_id) {
                $quiz = $theory->quizzes->first();

                $hasAttempted = QuizMember::where([
                    'member_id' => $member_id,
                    'quiz_id' => $quiz->id
                ])->exists();

                $is_finish = QuizMember::where([
                    'member_id' => $member_id,
                    'quiz_id' => $quiz->id,
                    'is_finish' => 1
                ])->exists();

                return [
                    'id' => $theory->theory_id,
                    'title' => $theory->title,
                    'short_description' => $theory->short_description,
                    'friendly_url' => $theory->friendly_url,
                    'picture' => $theory->picture,
                    'quiz' => [
                        'id' => $quiz->id,
                        'name' => $quiz->name,
                        'friendly_url' => $quiz->friendly_url,
                        'time' => $quiz->time,
                        'pointAward' => $quiz->pointAward,
                        'question_count' => $quiz->questions->count(),
                        'has_attempted' => $hasAttempted,
                        'is_finish' => $is_finish
                    ]
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function take_history_theory()
    {
        $member = Auth::guard('member')->user();
        if (!$member) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng đăng nhập để xem lịch sử'
            ], 401);
        }
    }
}
