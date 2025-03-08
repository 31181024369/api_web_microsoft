<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gift;
use Illuminate\Support\Facades\DB;
use App\Models\Member;

class GiftController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = Gift::query();
            $member = Auth::guard('member')->user();
            $memberPoints = $member ? $member->points : 0;

            $perPage = $request->input('per_page', 20);

            $gifts = $query->orderBy('id', 'desc')
                ->paginate($perPage);

            $gifts->through(function ($gift) use ($memberPoints) {
                $gift->makeHidden(['created_at', 'updated_at']);
                $gift->can_redeem = ($memberPoints - $gift->reward_point) >= 0;
                return $gift;
            });

            $response = [
                'status' => true,
                'member_points' => $memberPoints,
                'list' => $gifts->items(),
                'pagination' => [
                    'current_page' => $gifts->currentPage(),
                    'total_pages' => $gifts->lastPage(),
                    'per_page' => $gifts->perPage(),
                    'total' => $gifts->total(),
                ],
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function redeem(Request $request, string $id)
    {
        try {
            // Get authenticated member
            $member = Auth::guard('member')->user();
            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng đăng nhập để đổi quà'
                ], 401);
            }

            // Find gift
            $gift = Gift::find($id);
            if (!$gift) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy phần quà'
                ], 404);
            }

            // Check if member has enough points
            if ($member->points < $gift->reward_point) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không đủ điểm để đổi phần quà này',
                    'required_points' => $gift->reward_point,
                    'current_points' => $member->points,
                    'missing_points' => $gift->reward_point - $member->points
                ], 400);
            }

            // Begin transaction
            DB::beginTransaction();
            try {
                // Subtract points from member
                $member->points -= $gift->reward_point;
                // Add to used points
                $member->used_points = ($member->used_points ?? 0) + $gift->reward_point;
                $member->save();

                // Create redemption record if you have a table for it
                // GiftRedemption::create([
                //     'member_id' => $member->id,
                //     'gift_id' => $gift->id,
                //     'points_used' => $gift->reward_point,
                //     'redeemed_at' => now()
                // ]);

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Đổi quà thành công',
                    'data' => [
                        'gift' => $gift,
                        'remaining_points' => $member->points,
                        'used_points' => $member->used_points
                    ]
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi đổi quà',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
