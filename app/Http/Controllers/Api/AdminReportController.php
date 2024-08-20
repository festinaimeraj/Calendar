<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;

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
            $totalDaysUsed = DB::table('leave_requests')
            ->where('user_id', $user->id)
            ->where('leave_type', '!=', 'flex')
            ->where('answer', '!=', 'pending') 
            ->selectRaw('SUM(DATEDIFF(end_date, start_date) + 1) as aggregate')
            ->value('aggregate');
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

    public function showReport() {
        
        $requestsGrouped = LeaveRequest::with('user') // Eager load the related user
        ->get()
        ->groupBy(function ($leave) {
            return $leave->user->username; // Group by the user's username
        })
        ->map(function ($leaves, $username) {
            return $leaves->map(function ($leave) {
                return [
                    'leave_type' => $leave->leave_type,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'requested_days' => $leave->start_date->diffInDays($leave->end_date) + 1, // Calculate requested days
                    'answer' => $leave->answer, // Assuming there is a 'status' column
                    'action' => $leave->action, // Assuming there is an 'action' column
                ];
            })->toArray();
        })
        ->toArray();
        return response()->json($requestsGrouped);
    }   
}