<?php
namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;

class LeaveController extends Controller
{
    public function showLeaveTotals() {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }
        $leaveTypes = LeaveType::whereIn('name', ['Pushim', 'Pushim mjeksor', 'Tjeter'])->pluck('id');

        $leaveTotals = LeaveRequest::select('leave_type', DB::raw('SUM(DATEDIFF(end_date, start_date) + 1) AS total_days'))
            ->where('user_id', $user->id)
            ->where('answer', 'approved')
            ->whereIn('leave_type', $leaveTypes)
            ->groupBy('leave_type')
            ->get();

        $totalDays = $leaveTotals->sum('total_days');
        $remainingDays = 18 - $totalDays;


        return view('employee.my-leave-totals', compact('leaveTotals', 'remainingDays'));
    }
}
