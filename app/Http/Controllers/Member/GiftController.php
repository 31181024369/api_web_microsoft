<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Models\Gift;
use App\Models\Member;
use App\Models\GiftHistory;
use App\Mail\GiftRedeemMail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiftController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Gift::query();
            $member = Auth::guard('member')->user();
            $memberPoints = $member ? $member->points : 0;

            $perPage = $request->input('per_page', 20);

            $gifts = $query->where('display', 1)
                ->orderBy('reward_point', 'asc')
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $gifts->through(function ($gift) use ($memberPoints) {
                $gift->makeHidden(['created_at', 'updated_at']);
                $gift->can_redeem = ($memberPoints - $gift->reward_point) >= 0 && $gift->quantity > 0;
                $gift->is_available = $gift->quantity > 0;
                return $gift;
            });

            $response = [
                'status' => true,
                'data' => [
                    'member_points' => $memberPoints,
                    'list' => $gifts->items(),
                    'pagination' => [
                        'current_page' => $gifts->currentPage(),
                        'total_pages' => $gifts->lastPage(),
                        'per_page' => $gifts->perPage(),
                        'total' => $gifts->total(),
                    ],
                ]
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
            $member = Auth::guard('member')->user();
            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng đăng nhập để đổi quà'
                ], 401);
            }

            $hasRedeemed = GiftHistory::where('member_id', $member->id)
                ->where('gift_id', $id)
                ->exists();

            if ($hasRedeemed) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn chỉ có thể đổi quà một lần'
                ], 400);
            }

            $gift = Gift::lockForUpdate()->findOrFail($id);

            if ($gift->quantity <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Phần quà này hiện tại đã hết'
                ], 400);
            }

            if ($member->points < $gift->reward_point) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không đủ điểm để đổi phần quà này',
                    'data' => [
                        'required_points' => $gift->reward_point,
                        'current_points' => $member->points,
                        'missing_points' => $gift->reward_point - $member->points
                    ]
                ], 400);
            }

            $updatedMember = null;

            DB::transaction(function () use ($member, $gift, &$updatedMember, $request) {
                try {
                    $gift->decrement('quantity');

                    Member::where('id', $member->id)
                        ->update([
                            'points' => DB::raw('points - ' . $gift->reward_point),
                            'used_points' => DB::raw('COALESCE(used_points, 0) + ' . $gift->reward_point)
                        ]);

                    GiftHistory::create([
                        'member_id' => $member->id,
                        'gift_id' => $gift->id,
                        'points_used' => $gift->reward_point,
                        'remaining_points' => $member->points - $gift->reward_point,
                        'redeemed_at' => now(),
                        'cityAddress' => $request->cityAddress ?? null,
                        'districtAddress' => $request->districtAddress ?? null,
                        'wardAddress' => $request->wardAddress ?? null,
                        'streetAddress' => $request->streetAddress ?? null,
                        'numberPhone' => $request->numberPhone ?? null,
                    ]);
                    $fullAddress = array_filter([
                        $request->streetAddress,
                        $request->wardAddress,
                        $request->districtAddress,
                        $request->cityAddress
                    ]);

                    $emailData = [
                        'recipientName' => $member->username,
                        'giftName' => $gift->title,
                        'giftDescription' => $gift->description,
                        'redeemTime' => now()->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s'),
                        'rewardPoints' => $gift->reward_point,
                        'deliveryInfo' => 'Quà tặng sẽ được gửi đến sau khi chúng tôi xác nhận.',
                        'address' => !empty($fullAddress) ? implode(', ', $fullAddress) : 'Chưa cung cấp',
                        'phoneNumber' => $request->numberPhone
                    ];

                    try {
                        Mail::to($member->email)->send(new GiftRedeemMail($emailData));
                    } catch (\Exception $e) {
                        Log::error('Email error: ' . $e->getMessage());
                    }
                } catch (\Exception $e) {
                    Log::error('Transaction failed: ' . $e->getMessage());
                    throw $e;
                }
            });

            $updatedMember = Member::find($member->id);

            return response()->json([
                'status' => true,
                'message' => 'Đổi quà thành công',
                'data' => [
                    'gift' => $gift->makeHidden(['created_at', 'updated_at']),
                    'points' => [
                        'remaining' => $updatedMember->points,
                        'used' => $updatedMember->used_points ?? 0
                    ]
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy phần quà'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gift redemption failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi đổi quà'
            ], 500);
        }
    }
}
