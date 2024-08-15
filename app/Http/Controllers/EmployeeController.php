<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest; 
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


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
        return view('employee.request_leave');
    }

    public function submitLeaveRequest(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string|in:Pushim,Flex,Pushim mjeksor,Tjeter',
            'start_date' => 'required',
            'end_date' => 'required',
            'reason' => 'required|string|max:500',
        ]);
        
        $validLeaveTypes = ['Pushim', 'Flex', 'Pushim mjeksor', 'Tjeter'];
        if (!in_array($request->leave_type, $validLeaveTypes)) {
            return redirect()->back()->withErrors(['leave_type' => 'Invalid leave type.']);
        }

        $user = Auth::user();
        $startDate = \DateTime::createFromFormat('d-M-Y', $request->start_date)->format('Y-m-d');
        $endDate = \DateTime::createFromFormat('d-M-Y', $request->end_date)->format('Y-m-d');


        try {
            $leaveRequest = new LeaveRequest();
            $leaveRequest->user_id =  Auth::user()->id;
            $leaveRequest->leave_type = $request->leave_type;
            $leaveRequest->start_date = $startDate;
            $leaveRequest->end_date = $endDate;
            $leaveRequest->reason = $request->reason;
            $leaveRequest->answer = 'pending';
            $leaveRequest->save();

            // $adminEmail = 'admin@example.com'; // Replace with actual admin email
            // $subject = 'New Leave Request';
            // $body = "
            // <html>
            //     <head>
            //         <style>
            //             .email-container {
            //                 font-family: Arial, sans-serif;
            //                 line-height: 1.6;
            //                 color: #333;
            //             }
            //             .email-header {
            //                 background-color: #f2f2f2;
            //                 padding: 10px;
            //                 text-align: center;
            //             }
            //             .email-body {
            //                 padding: 20px;
            //             }
            //             .email-footer {
            //                 background-color: #f2f2f2;
            //                 padding: 10px;
            //                 text-align: center;
            //             }
            //             .email-title {
            //                 color: #444;
            //             }
            //         </style>
            //     </head>
            //     <body>
            //         <div class='email-container'>
            //             <div class='email-header'>
            //                 <h2 class='email-title'>New Leave Request</h2>
            //             </div>
            //             <div class='email-body'>
            //                 <p>Dear Admin,</p>
            //                 <p><strong>{$user->name}</strong> has requested leave.</p>
            //                 <p><strong>Leave Type:</strong> {$request->leave_type}</p>
            //                 <p><strong>Start Date:</strong> {$startDate}</p>
            //                 <p><strong>End Date:</strong> {$endDate}</p>
            //                 <p><strong>Reason:</strong> {$request->reason}</p>
            //             </div>
            //             <div class='email-footer'>
            //                 <p>This is an automated message. Please do not reply.</p>
            //             </div>
            //         </div>
            //     </body>
            // </html>
            // ";

            // Mail::send([], [], function($message) use ($adminEmail, $subject, $body) {
            //     $message->to($adminEmail)
            //             ->subject($subject)
            //             ->setBody($body, 'text/html');
            // });

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
        $leaveTotals = LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->selectRaw('leave_type, SUM(DATEDIFF(end_date, start_date) + 1) as total_days')
            ->groupBy('leave_type')
            ->get();

        $totalAllocatedDays = 18;
        $totalUsedDays = $leaveTotals->sum('total_days');
        $remainingDays = $totalAllocatedDays - $totalUsedDays;

        return view('employee.my-leave-totals', compact('leaveTotals', 'remainingDays'));
    }

    public function editMyRequests()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        DB::enableQueryLog();

$leaveRequests = LeaveRequest::where('user_id', $user->id)
                             ->where('status', 'pending')
                             ->get();

    Log::info(DB::getQueryLog());


        // Initialize the request to be edited as null
        $editRequest = null;

        // Check if an 'id' is present in the request
        if (request()->has('id')) {
            // Fetch the specific leave request for editing
            $editRequest = LeaveRequest::where('id', request('id'))
                                       ->where('user_id', $user->id)
                                       ->where('status', 'pending')
                                       ->first();
        }

        // Pass data to the view
        return view('employee.edit-my-requests', compact('leaveRequests', 'editRequest'));
    }
}
