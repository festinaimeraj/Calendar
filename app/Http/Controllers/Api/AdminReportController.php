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
            return $query->where('username', 'LIKE', "%{$username}%");
        })->get();
    
        $results = [];
    
        foreach ($users as $user) {
            $totalDaysUsed = DB::table('leave_requests')
                ->join('leave_types', 'leave_requests.leave_type', '=', 'leave_types.id')
                ->where('user_id', $user->id)
                ->where('leave_types.name', '!=', 'flex')
                ->where('answer', '!=', 'pending')
                ->where('answer' , '!=', 'denied')
                ->selectRaw('leave_types.name as leave_type, SUM(DATEDIFF(end_date, start_date) + 1) as aggregate')
                ->groupBy('leave_types.name')
                ->get();
    
                $leaveRequests = DB::table('leave_requests')
                ->join('leave_types', 'leave_requests.leave_type', '=', 'leave_types.id') 
                ->where('user_id', $user->id)
                ->when($leaveType, function ($query) use ($leaveType) {
                    return $query->where('leave_types.name', $leaveType);  
                })
                ->select('leave_types.name as leave_type', 'leave_requests.start_date', 'leave_requests.end_date', 'leave_requests.answer', DB::raw('DATEDIFF(end_date, start_date) + 1 as requested_days'))
                ->get();
    
            $results[] = [
                'username' => $user->username,
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'total_days_used' => $totalDaysUsed,
                'leave_requests' => $leaveRequests,
            ];
        }
    
        return response()->json([
            'users' => $results,
            'status' => true,
            'message' => 'Report generated successfully',
        ]);
    }
    
    public function showReport() {
        
        $requestsGrouped = LeaveRequest::with('user', 'type') 
        ->get()
        ->groupBy(function ($leave) {
            return $leave->user->username; 
        })
        ->map(function ($leaves, $username) {
            return $leaves->map(function ($leave) {
                return [
                    'leave_type' => $leave->type->name,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'requested_days' => $leave->start_date->diffInDays($leave->end_date) + 1, 
                    'answer' => $leave->answer, 
                    'action' => $leave->action, 
                ];
            })->toArray();
        })
        ->toArray();
        return response()->json([
            'requests_grouped' => $requestsGrouped,
            'success' => true,
            'message' => 'Report generated successfully',
        ]);
    }   
}