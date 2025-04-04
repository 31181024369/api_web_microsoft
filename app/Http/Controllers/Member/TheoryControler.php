<?php

namespace App\Http\Controllers\Member;

use App\Models\TheOryCategory;
use App\Models\TheOry;
use App\Models\Quiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizMember;

class TheoryControler extends Controller
{
    public function index()
    {
        try {
            $member_id = Auth::guard('member')->user()?->id;

            $theOryCategory = TheOryCategory::where('display', 1)
                ->with(['theories' => function ($q) {
                    $q->where('display', 1)
                        ->whereHas('quizzes', function ($query) {
                            $query->where('display', 1);
                        })
                        ->with(['quizzes' => function ($q) {
                            $q->where('display', 1)
                                ->select([
                                    'id',
                                    'theory_id',
                                    'cat_id',
                                    'name',
                                    'friendly_url',
                                    'time',
                                    'pointAward',
                                    'display'
                                ])
                                ->with('questions:id,quiz_id');
                        }])
                        ->select([
                            'theory_id',
                            'cat_id',
                            'title',
                            'friendly_url',
                            'picture',
                            'short_description',
                            'created_at'
                        ])
                        ->orderBy('theory_id', 'desc');
                }])
                ->get();

            $response = [];

            foreach ($theOryCategory as $category) {
                $theories = $category->theories
                    ->map(function ($theory) use ($member_id) {
                        $quiz = $theory->quizzes->first(function ($quiz) use ($theory) {
                            return $quiz->cat_id == $theory->cat_id
                                && $quiz->theory_id == $theory->theory_id
                                && $quiz->display == 1;
                        });

                        if (!$quiz) return null;

                        $hasAttempted = false;
                        $is_finish = false;

                        if ($member_id) {
                            $quizMember = QuizMember::where([
                                'member_id' => $member_id,
                                'quiz_id' => $quiz->id
                            ])->first();

                            if ($quizMember) {
                                $hasAttempted = true;
                                $is_finish = $quizMember->is_finish;
                            }
                        }

                        return [
                            'id' => $theory->theory_id,
                            'title' => $theory->title,
                            'friendly_url' => $theory->friendly_url,
                            'picture' => $theory->picture,
                            'short_description' => $theory->short_description,
                            'created_at' => \Carbon\Carbon::parse($theory->created_at)->format('d/m/Y'),
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
                    })
                    ->filter()
                    ->values()
                    ->take(10);

                if ($theories->isNotEmpty()) {
                    $response[] = [
                        'id' => $category->cat_id,
                        'title' => $category->title,
                        'theories' => $theories
                    ];
                }
            }

            return response()->json([
                'status' => true,
                'list' => $response
            ], 200);
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
                ->with(['category', 'quizzes' => function ($query) {
                    $query->select('id', 'theory_id', 'friendly_url', 'name', 'display','expirationDate')
                        ->with('questions:id,quiz_id');
                }])
                ->first();

            if (!$theory) {
                return response()->json([
                    'status' => false,
                    'error' => 'Không tìm thấy bài học'
                ], 404);
            }

            if (!$theory->display || !$theory->category->display) {
                return response()->json([
                    'status' => false,
                    'error' => 'Bài học này hiện không khả dụng'
                ], 403);
            }

            $displayedQuiz = $theory->quizzes->firstWhere('display', 1);

            if (!$displayedQuiz) {
                return response()->json([
                    'status' => false,
                    'error' => 'Bài học này hiện không khả dụng'
                ], 403);
            }
            return $displayedQuiz;

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
                    'quiz' => [
                        'id' => $displayedQuiz->id,
                        'name' => $displayedQuiz->name,
                        'friendly_url' => $displayedQuiz->friendly_url,
                        'question_count' => $displayedQuiz->questions->count(),
                        'expirationDate' => $quiz->expirationDate && $quiz->expirationDate < now()->timestamp ? 'Hết hạn' : $quiz->expirationDate,
                    ]
                ]
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function take5theory()
    {
        try {
            $member_id = Auth::guard('member')->id();

            $theories = Theory::where('display', 1)
                ->whereHas('category', function ($q) {
                    $q->where('display', 1);
                })
                ->whereHas('quizzes', function ($q) {
                    $q->where('display', 1);
                })
                ->with(['quizzes' => function ($query) {
                    $query->where('display', 1)
                        ->select('id', 'theory_id', 'name', 'friendly_url', 'time', 'pointAward')
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

    public function showCategory(string $id, Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);

            $category = TheOryCategory::where('cat_id', $id)
                ->where('display', 1)
                ->whereHas('theories', function ($query) {
                    $query->where('display', 1)
                        ->whereHas('quizzes', function ($q) {
                            $q->where('display', 1);
                        });
                })
                ->first();

            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Danh mục hiện không khả dụng'
                ], 404);
            }

            $theories = TheOry::where('cat_id', $id)
                ->where('display', 1)
                ->whereHas('quizzes', function ($q) {
                    $q->where('display', 1);
                })
                ->with(['quizzes' => function ($query) {
                    $query->where('display', 1)
                        ->select('id', 'theory_id', 'name', 'friendly_url', 'time', 'pointAward')
                        ->with('questions:id,quiz_id');
                }])
                ->select('theory_id', 'title', 'short_description', 'friendly_url', 'picture', 'cat_id')
                ->orderBy('theory_id', 'desc')
                ->paginate($perPage);

            if ($theories->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có bài học nào trong danh mục này'
                ], 404);
            }

            $data = $theories->through(function ($theory) {
                $quiz = $theory->quizzes->first();

                if (!$quiz) {
                    return null;
                }

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
                    ]
                ];
            });

            return response()->json([
                'status' => true,
                'data' => [
                    'category' => [
                        'id' => $category->cat_id,
                        'title' => $category->title
                    ],
                    'theories' => $data->items(),
                    'pagination' => [
                        'current_page' => $theories->currentPage(),
                        'per_page' => $theories->perPage(),
                        'total' => $theories->total(),
                        'last_page' => $theories->lastPage()
                    ]
                ]
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

    public function take_category_theory()
    {
        try {
            $categories = TheOryCategory::where('display', 1)
                ->with(['theories' => function ($query) {
                    $query->where('display', 1)
                        ->select('theory_id', 'cat_id', 'title', 'friendly_url');
                }])
                ->select('cat_id', 'title', 'friendly_url')
                ->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No categories found or displayed'
                ], 404);
            }

            $result = $categories->map(function ($category) {
                return [
                    'cat_id' => $category->cat_id,
                    'title' => $category->title,
                    'friendly_url' => $category->friendly_url,
                    'theories' => $category->theories->map(function ($theory) {
                        return [
                            'theory_id' => $theory->theory_id,
                            'title' => $theory->title,
                            'friendly_url' => $theory->friendly_url
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
