<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberLog;

class MemberLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $username = $request->input('data');
            $startTime = $request->input('start_time');
            $endTime = $request->input('end_time');
            $perPage = $request->input('per_page', 10);

            $query = MemberLog::with('member:id,username');

            if ($username) {
                $query->whereHas('member', function ($q) use ($username) {
                    $q->where('username', 'like', '%' . $username . '%');
                });
            }

            if ($startTime && $endTime) {
                $start = \Carbon\Carbon::createFromTimestamp((int)$startTime)
                    ->setTimezone('Asia/Ho_Chi_Minh')
                    ->startOfDay();
                $end = \Carbon\Carbon::createFromTimestamp((int)$endTime)
                    ->setTimezone('Asia/Ho_Chi_Minh')
                    ->endOfDay();

                $query->whereBetween('created_at', [$start, $end]);
            }

            $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

            $data = [
                'data' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'total_pages' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                ],
            ];

            return response()->json([
                'status' => true,
                'member_log' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
