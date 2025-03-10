<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftHistory;

class GifthistoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = GiftHistory::query();
            $perPage = $request->input('per_page', 10);
            $data = $request->input('data');
            $startTime = $request->input('start_time');
            $endTime = $request->input('end_time');

            if ($data || $startTime || $endTime) {
                $query->where(function ($query) use ($data, $startTime, $endTime) {
                    if ($data) {
                        $query->whereHas('member', function ($q) use ($data) {
                            $q->where(function ($innerQ) use ($data) {
                                $innerQ->where('username', 'LIKE', "%{$data}%")
                                    ->orWhere('email', 'LIKE', "%{$data}%")
                                    ->orWhere('phone', 'LIKE', "%{$data}%");
                            });
                        });
                    }

                    if ($startTime && $endTime) {
                        $query->whereBetween('redeemed_at', [$startTime, $endTime]);
                    } elseif ($startTime) {
                        $query->whereDate('redeemed_at', '>=', $startTime);
                    } elseif ($endTime) {
                        $query->whereDate('redeemed_at', '<=', $endTime);
                    }
                });
            }

            $giftHistories = $query->with([
                'member',
                'gift'
            ])
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $formattedHistories = $giftHistories->through(function ($item) {
                return [
                    'id' => $item->id,
                    'member' => [
                        'id' => $item->member->id,
                        'username' => $item->member->username,
                        'mem_code' => $item->member->mem_code,
                        'email' => $item->member->email,
                        'address' => $item->member->address,
                        'company' => $item->member->company,
                        'full_name' => $item->member->full_name,
                        'avatar' => $item->member->avatar,
                        'phone' => $item->member->phone,
                        'provider' => $item->member->provider,
                        'district' => $item->member->district,
                        'ward' => $item->member->ward,
                        'city_province' => $item->member->city_province,
                        'nameCompany' => $item->member->nameCompany,
                        'tax' => $item->member->tax,
                        'status' => $item->member->status,
                        'm_status' => $item->member->m_status,
                        'points' => $item->member->points,
                        'used_points' => $item->member->used_points,
                        'number_passes' => $item->member->number_passes,
                        'date_join' => $item->member->date_join,
                        'password_token' => $item->member->password_token
                    ],
                    'gift' => $item->gift ? [
                        'id' => $item->gift->id,
                        'title' => $item->gift->title,
                        'description' => $item->gift->description,
                        'picture' => $item->gift->picture,
                        'reward_point' => $item->gift->reward_point,
                    ] : null,
                    'points_used' => $item->points_used,
                    'remaining_points' => $item->remaining_points,
                    'redeemed_at' => $item->redeemed_at,
                    'status' => $item->is_confirmed ? 'Đã xác nhận' : 'Chờ xác nhận',
                    'confirm_at' => $item->confirmed_at?->format('d/m/Y H:i:s')
                ];
            });

            $response = [
                'status' => true,
                'data' => [
                    'list' => $formattedHistories->values(),
                    'pagination' => [
                        'current_page' => $giftHistories->currentPage(),
                        'total_pages' => $giftHistories->lastPage(),
                        'per_page' => $giftHistories->perPage(),
                        'total' => $giftHistories->total(),
                    ],
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

    public function confirm($id)
    {
        try {
            $giftHistory = GiftHistory::findOrFail($id);

            if ($giftHistory->is_confirmed) {
                return response()->json([
                    'status' => false,
                    'message' => 'Quà đã được xác nhận'
                ], 400);
            }

            $giftHistory->update([
                'is_confirmed' => true,
                'confirmed_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Xác nhận quà thành công',
                'data' => [
                    'id' => $giftHistory->id,
                    'confirmed_at' => $giftHistory->confirmed_at->format('d/m/Y H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
