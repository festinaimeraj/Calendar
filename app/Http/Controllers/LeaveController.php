<?php
namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;

class LeaveController extends Controller
{
    public function showLeaveTotals()
    {
        $userId = Auth::id();

        $leaveTypeAllocations = LeaveType::pluck('max_days', 'id')->toArray();
      
        $leaveTotals = LeaveRequest::where('user_id', $userId)
            ->where('answer', 'approved')
            ->selectRaw('leave_type, SUM(DATEDIFF(end_date, start_date) + 1) as total_days')
            ->groupBy('leave_type')
            ->get();

        
        $remainingDaysByType = [];
        
        foreach ($leaveTotals as $leave) {
            $allocatedDays = $leaveTypeAllocations[$leave->leave_type] ;
            $remainingDaysByType[$leave->leave_type] = $allocatedDays - $leave->total_days;            
        } 

        return view('employee.my-leave-totals', compact('leaveTotals', 'remainingDaysByType'));
    }
}
