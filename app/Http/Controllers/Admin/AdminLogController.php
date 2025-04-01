<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminLogs;
use Carbon\Carbon;

class AdminLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = AdminLogs::query();
            $perPage = $request->input('per_page', 10);
            $data = $request->input('data');
            $startTime = $request->input('start_time');
            $endTime = $request->input('end_time');

            if ($data || $startTime || $endTime) {
                $query->where(function ($query) use ($data, $startTime, $endTime) {
                    if ($data) {
                        $query->whereHas('member', function ($q) use ($data) {
                            $q->where(function ($innerQ) use ($data) {
                                $innerQ->where('username', 'LIKE', "%{$data}%");
                            });
                        });
                    }

                    if ($startTime && $endTime) {
                        try {
                            $start = \Carbon\Carbon::createFromTimestamp((int)$startTime)
                                ->setTimezone('Asia/Ho_Chi_Minh')
                                ->startOfDay();
                            $end = \Carbon\Carbon::createFromTimestamp((int)$endTime)
                                ->setTimezone('Asia/Ho_Chi_Minh')
                                ->endOfDay();
                            $query->whereBetween('created_at', [$start, $end]);
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Invalid date format'
                            ], 400);
                        }
                    } elseif ($startTime) {
                        try {
                            $start = \Carbon\Carbon::createFromTimestamp((int)$startTime)
                                ->setTimezone('Asia/Ho_Chi_Minh')
                                ->startOfDay();
                            $query->whereDate('created_at', '>=', $start);
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Invalid start date format'
                            ], 400);
                        }
                    } elseif ($endTime) {
                        try {
                            $end = \Carbon\Carbon::createFromTimestamp((int)$endTime)
                                ->setTimezone('Asia/Ho_Chi_Minh')
                                ->endOfDay();
                            $query->whereDate('created_at', '<=', $end);
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Invalid end date format'
                            ], 400);
                        }
                    }
                });
            }

            $query->orderBy('created_at', 'desc');
            $logs = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'data' => [
                    'list' => $logs->items(),
                    'pagination' => [
                        'current_page' => $logs->currentPage(),
                        'total_pages' => $logs->lastPage(),
                        'per_page' => $logs->perPage(),
                        'total' => $logs->total()
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
