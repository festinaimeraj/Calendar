<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeaveRequest;

class AdminReportController extends Controller
{
    public function search(Request $request) {
        $username = $request->input('username');
        $leaveType = $request->input('leave_type');

        $users = User::when($username, function ($query) use ($username) {
            return $query->where('username', 'LIKE', '%$username%'); 
        })->get();

        $results = [];
        foreach ($users as $user) {
            $totalDaysUsed = LeaveRequest::where('user_id', $user->id)
            ->where('leave_type', '!=', 'flex')
            ->sum('requested_days');

            // Get leave requests for the user and filter by leave type if provided
            $leaveRequests = LeaveRequest::where('user_id', $user->id)
                ->when($leaveType, function ($query) use ($leaveType) {
                    return $query->where('leave_type', $leaveType);
                })
                ->get();

            $result[] = [
                'username' => $user->username,
                'total_days_used' => $totalDaysUsed,
                'leave_requests' => $leaveRequests
            ];
        }

        return response()->json($result);
    }
}
