<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\QuizMember;
use App\Models\QuizHistory;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizHistoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $member = Auth::guard('member')->user();
            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng đăng nhập để xem lịch sử'
                ], 401);
            }

            $perPage = $request->input('per_page', 10);
            $quizCounts = QuizMember::where('member_id', $member->id)
                ->select(
                    DB::raw('COUNT(*) as total_attempted'),
                    DB::raw('SUM(CASE WHEN is_finish = 1 THEN 1 ELSE 0 END) as total_completed')
                )
                ->first();

            $quizHistory = QuizMember::where('quiz_member.member_id', $member->id)
                ->with(['quizzes' => function ($query) {
                    $query->select('id', 'name', 'pointAward', 'theory_id')
                        ->with('theory:theory_id,title');
                }])
                ->join('history', function ($join) {
                    $join->on('quiz_member.member_id', '=', 'history.member_id')
                        ->on('quiz_member.quiz_id', '=', 'history.quiz_id')
                        ->on('quiz_member.times', '=', 'history.times');
                })
                ->select(
                    'quiz_member.id',
                    'quiz_member.member_id',
                    'quiz_member.quiz_id',
                    'quiz_member.is_finish',
                    'quiz_member.time_start',
                    'quiz_member.time_end',
                    'quiz_member.times',
                    'history.total_questions',
                    'history.total_correct'
                )
                ->orderBy('quiz_member.id', 'desc')
                ->paginate($perPage);

            $formattedHistory = $quizHistory->through(function ($item) {
                $startTime = null;
                if ($item->time_start) {
                    try {
                        $startTime = is_numeric($item->time_start)
                            ? \Carbon\Carbon::createFromTimestamp((int)($item->time_start / 1000))
                            ->setTimezone('Asia/Ho_Chi_Minh')
                            ->format('d/m/Y H:i:s')
                            : \Carbon\Carbon::parse($item->time_start)
                            ->setTimezone('Asia/Ho_Chi_Minh')
                            ->format('d/m/Y H:i:s');
                    } catch (\Exception $e) {
                        $startTime = null;
                    }
                }

                $score = 0;
                $isPassed = false;
                if ($item->total_questions > 0) {
                    $score = ($item->total_correct / $item->total_questions) * 100;
                    $isPassed = $score >= 80;
                }

                $rewardPoints = 0;
                if ($item->is_finish && $isPassed && isset($item->quizzes->pointAward)) {
                    $rewardPoints = $item->quizzes->pointAward;
                }

                return [
                    'name' => $item->quizzes->name ?? 'Không tìm thấy bài thi',
                    'theory' => $item->quizzes->theory->title ?? 'Không có bài học',
                    'score' => round($score, 2),
                    'time_start' => $startTime ?? 'Chưa bắt đầu',
                    'reward_point' => $rewardPoints,
                    'times' => $item->times,
                    //'is_finished' => $item->is_finish ? 'true' : 'false',
                    'is_passed' => $isPassed,
                    'result' => [
                        'toltal_question' => $item->total_questions,
                        'total_correct' => $item->total_correct
                    ]
                ];
            });

            $response = [
                'status' => true,
                'data' => [
                    'summary' => [
                        'total_attempt' => (int)$quizCounts->total_attempted,
                        'total_complete' => (int)$quizCounts->total_completed
                    ],
                    'list' => $formattedHistory->values(),
                    'pagination' => [
                        'current_page' => $quizHistory->currentPage(),
                        'total_pages' => $quizHistory->lastPage(),
                        'per_page' => $quizHistory->perPage(),
                        'total' => $quizHistory->total(),
                    ]
                ]
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
