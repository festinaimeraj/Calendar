<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use \Illuminate\Support\Facades\Log;

class MyLeaveTotalsController extends Controller
{
    public function getLeaveTotals()
    {
        $userId = Auth::id(); 
        
        $leaveTypes = LeaveRequest::select('leave_type')
                                  ->where('user_id', $userId)
                                  ->where('answer', 'approved')
                                  ->distinct()
                                  ->pluck('leave_type');

        $leaveTotals = [];

        foreach ($leaveTypes as $leaveType) {
            $requests = LeaveRequest::where('leave_type', $leaveType)
                                    ->where('user_id', $userId)
                                    ->where('answer', 'approved')
                                    ->get();

            $totalDaysUsed = $requests->sum(function($request) {
                return Carbon::parse($request->end_date)->diffInDays(Carbon::parse($request->start_date)) + 1;
            });

            $maxLeaveDays = LeaveType::where('id', $leaveType)->value('max_days');
            
            Log::info("Leave Type: $leaveType, Max Days: $maxLeaveDays");

            $maxLeaveDays = $maxLeaveDays ?? 0;

            $remainingDays = $maxLeaveDays - $totalDaysUsed;

            $leaveTotals[] = [
                "leave_type" => $leaveType,
                "total_days_used" => $totalDaysUsed,
                "remaining_days" => $remainingDays,
                "max_days" => $maxLeaveDays, 
            ];
        }

        return response()->json([
            "leave_totals" => $leaveTotals,
            "status" => true,
            'message' => 'Leave totals retrieved successfully.'
        ]);
    }
}
