<?php

namespace App\Http\Controllers\Member;

use Illuminate\Support\Facades\Auth;
use App\Models\GiftHistory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GiftHiststoryController extends Controller
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

            $query = GiftHistory::query()
                ->where('member_id', $member->id)
                ->whereHas('gift')
                ->with('gift:id,title,reward_point,picture');

            $perPage = $request->input('per_page', 5);
            $histories = $query->orderBy('id', 'desc')->paginate($perPage);

            $filteredItems = collect($histories->items())->filter(function ($item) {
                return $item->gift !== null;
            });

            $response = [
                'status' => true,
                'data' => [
                    'member_points' => [
                        'current' => $member->points,
                        'used' => $member->used_points ?? 0
                    ],
                    'list' => $filteredItems->values(),
                    'pagination' => [
                        'current_page' => $histories->currentPage(),
                        'total_pages' => $histories->lastPage(),
                        'per_page' => $histories->perPage(),
                        'total' => $histories->total(),
                    ],
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
