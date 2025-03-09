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
                'member:id,name,email,points,used_points',
                'gift:id,title'
            ])
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $response = [
                'status' => true,
                'list' => $giftHistories->items(),
                'pagination' => [
                    'current_page' => $giftHistories->currentPage(),
                    'total_pages' => $giftHistories->lastPage(),
                    'per_page' => $giftHistories->perPage(),
                    'total' => $giftHistories->total(),
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
}
