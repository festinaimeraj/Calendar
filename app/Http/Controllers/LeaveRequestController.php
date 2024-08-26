<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class LeaveRequestController extends Controller
{
    public function showRequestForm()
    {
        $leaveTypes = LeaveType::all();

        return view('employee.request_leave', compact('leaveTypes'));
    }


    public function update(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'leave_type' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
    
        // Fetch the leave request being updated
        $leaveRequest = LeaveRequest::find($request->requestId);
    
        // Ensure the leave request belongs to the authenticated user
        if ($leaveRequest->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['error' => 'You are not authorized to edit this request.']);
        }
    
        // Update the leave request details
        $leaveRequest->leave_type = $request->leave_type;
        $leaveRequest->start_date = $request->start_date;
        $leaveRequest->end_date = $request->end_date;
        $leaveRequest->save();
    
        // Redirect back with a success message
        return redirect()->route('employee.edit-my-requests')->with('status', 'Leave request updated successfully.');
    }
}
