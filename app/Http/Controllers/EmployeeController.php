<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest; 
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;


class EmployeeController extends Controller
{
    
    public function index()
    {
        return view('employee.index');
    }

    public function calendar()
    {
        return view('employee.calendar');
    }


    public function requestLeave()
    {
        $leaveTypes = LeaveType::all();

        return view('employee.request_leave', compact('leaveTypes'));
    }

    public function submitLeaveRequest(Request $request)
    {
        $currentYear = date('Y');

        
    $request->validate([
        'leave_type' => 'required',
        'start_date' => [
            'required',
            'date_format:d/m/Y',
            'before_or_equal:31/12/'.$currentYear,
            'after_or_equal:01/01/'.$currentYear,
        ],
        'end_date' => [
            'required',
            'date_format:d/m/Y',
            'before_or_equal:31/12/'.$currentYear,
            'after_or_equal:start_date',
        ],
        'reason' => 'required|string|max:500',
    ], [
        'start_date.before_or_equal' => 'The start date must be within the current year ('.$currentYear.').',
        'start_date.after_or_equal' => 'The start date must be within the current year ('.$currentYear.').',
        'end_date.before_or_equal' => 'The end date must be within the current year ('.$currentYear.').',
        'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
    ]);

        $user = Auth::user();
        $startDate = \DateTime::createFromFormat('d/m/Y', $request->start_date);
        $endDate = \DateTime::createFromFormat('d/m/Y', $request->end_date);

        if ($startDate === false || $endDate === false) {
            // Handle the error if the date creation failed
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Format the date to 'Y-m-d' format
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');

        // Check if there's a pending leave request that overlaps with the new request dates
        $existingRequest = LeaveRequest::where('user_id', $user->id)
        ->where('answer', 'pending') // Ensure the status is pending
        ->where(function($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                });
        })
        ->first();

    if ($existingRequest) {
        return redirect()->back()->withErrors(['error' => 'You have already submitted a leave request that overlaps with these dates.']);
    }

        try {
            $leaveRequest = new LeaveRequest();
            $leaveRequest->user_id =  Auth::user()->id;
            $leaveRequest->leave_type = $request->leave_type;
            $leaveRequest->start_date = $startDate;
            $leaveRequest->end_date = $endDate;
            $leaveRequest->reason = $request->reason;
            $leaveRequest->answer = 'pending';
            $leaveRequest->save();

            $adminEmail = 'festinaimeraj1@gmail.com'; // Replace with actual admin email
            $subject = 'New Leave Request';
            $body = "
            <html>
                <head>
                    <style>
                        .email-container {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            color: #333;
                        }
                        .email-header {
                            background-color: #f2f2f2;
                            padding: 10px;
                            text-align: center;
                        }
                        .email-body {
                            padding: 20px;
                        }
                        .email-footer {
                            background-color: #f2f2f2;
                            padding: 10px;
                            text-align: center;
                        }
                        .email-title {
                            color: #444;
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='email-header'>
                            <h2 class='email-title'>New Leave Request</h2>
                        </div>
                        <div class='email-body'>
                            <p>Dear Admin,</p>
                            <p><strong>{$user->name}</strong> has requested leave.</p>
                            <p><strong>Leave Type:</strong> {$request->leave_type}</p>
                            <p><strong>Start Date:</strong> {$startDateFormatted}</p>
                            <p><strong>End Date:</strong> {$endDateFormatted}</p>
                            <p><strong>Reason:</strong> {$request->reason}</p>
                        </div>
                        <div class='email-footer'>
                            <p>This is an automated message. Please do not reply.</p>
                        </div>
                    </div>
                </body>
            </html>
            ";

            Mail::send([], [], function($message) use ($adminEmail, $subject, $body) {
                $message->to($adminEmail)
                        ->subject($subject)
                        ->html($body);
            });

            // Set a success message in the session
            session()->flash('status', 'Your leave request has been submitted successfully and is pending approval.');

            // Redirect based on the role
            if ($user->role === 'admin') {
                return redirect()->back();
            } else {
                return redirect()->back();
            } 
        } catch (\Exception $e) {
            Log::error('Error saving leave request: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to save leave request.']);
        }
    }



    public function myLeaveTotals()
    {
        
        $userId = Auth::id();

        $leaveTypeAllocations =[
            1 => 18,
            2 => 15,
            3 => 12,
            4 => 10,
        ];
      
        $leaveTotals = LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->selectRaw('leave_type, SUM(DATEDIFF(end_date, start_date) + 1) as total_days')
            ->groupBy('leave_type')
            ->get();

        
        $remainingDaysByType = [];
        foreach ($leaveTotals as $leave) {
            $allocatedDays = $leaveTypeAllocations[$leave->leave_types] ?? 0;
            $remainingDaysByType[$leave->leave_types] = $allocatedDays - $leave->total_days;            
        }

        return view('employee.my-leave-totals', compact('leaveTotals', 'remainingDaysByType'));
    }

    public function editMyRequests()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        // Retrieve leave requests that are pending
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
                                    ->where('answer', 'pending')
                                    ->join('leave_types', 'leave_requests.leave_type', '=', 'leave_types.id')
                                    ->select('leave_requests.*', 'leave_types.name as leave_type_name')
                                    ->get();

        // Initialize the request to be edited as null
        $editRequest = null;

        // Check if an 'id' is present in the request
        if (request()->has('id')) {
            // Fetch the specific leave request for editing
            $editRequest = LeaveRequest::where('id', request('id'))
                                    ->where('user_id', $user->id)
                                    ->where('answer', 'pending')
                                    ->first();
        }

        // Fetch all leave types
        $leaveTypes = LeaveType::all();

        // Pass data to the view
        return view('employee.edit-my-requests', compact('leaveRequests', 'editRequest', 'leaveTypes'));
    }
}