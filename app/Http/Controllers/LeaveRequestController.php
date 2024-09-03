<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    public function showRequestForm()
    {
        $leaveTypes = LeaveType::all();

        return view('employee.request_leave', compact('leaveTypes'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|exists:leave_types,id',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
        ]);
    
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
    
        $leaveRequest = LeaveRequest::find($request->requestId);

        if ($leaveRequest->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['error' => 'You are not authorized to edit this request.']);
        }
    
        $leaveRequest->leave_type = $request->leave_type;
        $leaveRequest->start_date = $startDate;
        $leaveRequest->end_date = $endDate;
        $leaveRequest->save();
    
        return redirect()->route('employee.edit-my-requests')->with('status', 'Leave request updated successfully.');
    }
}
