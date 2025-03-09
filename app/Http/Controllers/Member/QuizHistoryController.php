<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\QuizMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            $query = QuizMember::query()
                ->where('member_id', $member->id)
                ->with('quiz:id,name,pointAward,time');

            $perPage = $request->input('per_page', 5);
            $histories = $query->orderBy('id', 'desc')->paginate($perPage);

            $response = [
                'status' => true,
                'data' => [
                    'list' => $histories->items()
                ],
                'pagination' => [
                    'current_page' => $histories->currentPage(),
                    'total_pages' => $histories->lastPage(),
                    'per_page' => $histories->perPage(),
                    'total' => $histories->total(),
                ],
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
