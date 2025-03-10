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
            $perPage = $request->input('per_page', 20);

            $giftHistories = $query->with([
                'member',
                'gift'
            ])
                ->whereHas('gift')
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $formattedHistories = $giftHistories->through(function ($item) {
                return [
                    'id' => $item->id,
                    'member' => [
                        'id' => $item->member->id,
                        'email' => $item->member->email,
                        'name' => $item->member->name,
                        'points' => $item->member->points,
                        'used_points' => $item->member->used_points,
                        'status' => $item->member->status
                    ],
                    'gift' => [
                        'id' => $item->gift->id,
                        'title' => $item->gift->title,
                        'description' => $item->gift->description,
                        'picture' => $item->gift->picture,
                        'reward_point' => $item->gift->reward_point,
                        'quantity' => $item->gift->quantity
                    ],
                    'points_used' => $item->points_used,
                    'remaining_points' => $item->remaining_points,
                    'redeemed_at' => $item->redeemed_at
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
}
