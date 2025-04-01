<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MemberLog;

class LogMemberAPI
{
    public function handle(Request $request, Closure $next)
    {
        $member = Auth::guard('member')->user();

        if ($member) {
            $url = $request->fullUrl();
            $path = $request->path();

            $cleanedPath = str_replace('api/member/', '', $path);

            $ipAddress = $request->ip();
            $module = 'unknown';
            $friendlyUrl = '';
            $action = '';

            \Log::info('LogMemberAPI Cleaned Path: ' . $cleanedPath);

            if (str_starts_with($cleanedPath, 'theorys/')) {
                $module = 'theory';
                $action = $request->segment(4);
                $friendlyUrl = 'https://vitinhnguyenkim.vn/edu/lesson/' . $action;
            } elseif (str_starts_with($cleanedPath, 'show-quiz-detail/')) {
                $module = 'quiz';
                $action = $request->segment(4);
                $friendlyUrl = 'https://vitinhnguyenkim.vn/edu/quiz/' . $action;
            }

            MemberLog::create([
                'friendly_url' => $friendlyUrl,
                'count' => 1,
                'member_id' => $member->id,
                'module' => $module,
                'action' => $action,
                'ip_address' => $ipAddress,
            ]);
        }

        return $next($request);
    }
}