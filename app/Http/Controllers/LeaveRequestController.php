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

    // public function submitLeaveRequest(Request $request)
    // {
    //     $request->validate([
    //         'leave_type' => 'required|string|in:Pushim,Flex,Pushim mjeksor,Tjeter',
    //         'start_date' => 'required|date_format:d/m/Y',
    //         'end_date' => 'required|date_format:d/m/Y',
    //         'reason' => 'required|string|max:500',
    //     ]);

    //     $user = Auth::user();
    //     $startDate = \DateTime::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
    //     $endDate = \DateTime::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');


    //     try {
    //         $leaveRequest = new LeaveRequest();
    //         $leaveRequest->user_id = $user->id; // Corrected user ID assignment
    //         $leaveRequest->leave_type = $request->leave_type;
    //         $leaveRequest->start_date = $startDate;
    //         $leaveRequest->end_date = $endDate;
    //         $leaveRequest->reason = $request->reason;
    //         $leaveRequest->status = 'pending';
    //         $leaveRequest->save();

    //         $adminEmail = 'admin@example.com';
    //         $subject = 'New Leave Request';
    //         $body = "
    //         <html>
    //             <head>
    //                 <style>
    //                     .email-container {
    //                         font-family: Arial, sans-serif;
    //                         line-height: 1.6;
    //                         color: #333;
    //                     }
    //                     .email-header {
    //                         background-color: #f2f2f2;
    //                         padding: 10px;
    //                         text-align: center;
    //                     }
    //                     .email-body {
    //                         padding: 20px;
    //                     }
    //                     .email-footer {
    //                         background-color: #f2f2f2;
    //                         padding: 10px;
    //                         text-align: center;
    //                     }
    //                     .email-title {
    //                         color: #444;
    //                     }
    //                 </style>
    //             </head>
    //             <body>
    //                 <div class='email-container'>
    //                     <div class='email-header'>
    //                         <h2 class='email-title'>New Leave Request</h2>
    //                     </div>
    //                     <div class='email-body'>
    //                         <p>Dear Admin,</p>
    //                         <p><strong>{$user->name}</strong> has requested leave.</p>
    //                         <p><strong>Leave Type:</strong> {$request->leave_type}</p>
    //                         <p><strong>Start Date:</strong> {$startDate}</p>
    //                         <p><strong>End Date:</strong> {$endDate}</p>
    //                         <p><strong>Reason:</strong> {$request->reason}</p>
    //                     </div>
    //                     <div class='email-footer'>
    //                         <p>This is an automated message. Please do not reply.</p>
    //                     </div>
    //                 </div>
    //             </body>
    //         </html>
    //         ";

    //         Mail::send([], [], function($message) use ($adminEmail, $subject, $body) {
    //             $message->to($adminEmail)
    //                     ->subject($subject)
    //                     ->setBody($body, 'text/html');
    //         });

    //         session()->flash('status', 'Your leave request has been submitted successfully and is pending approval.');

    //         return $user->role === 'admin'
    //             ? redirect()->route('admin.request-leave')
    //             : redirect()->route('employee.request-leave');
    //     } catch (\Exception $e) {
    //         Log::error('Error saving leave request: ' . $e->getMessage());
    //         return redirect()->back()->withErrors(['error' => 'Failed to save leave request.']);
    //     }
    // }

    // public function editMyRequests()
    // {
    //     $user = Auth::user();

    //     $leaveRequests = LeaveRequest::where('user_id', $user->id)
    //         ->where('answer', 'pending')
    //         ->get();

    //     $editRequest = null;
    //     if (request()->has('id')) {
    //         $editRequest = LeaveRequest::where('id', request('id'))
    //             ->where('user_id', $user->id)
    //             ->where('answer', 'pending')
    //             ->first();
    //     }
    //     $leaveTypes = LeaveType::all(); // Fetch leave types for the dropdown
    //     return view('employee.edit-my-requests', compact('leaveRequests', 'editRequest', 'leaveTypes'));
    // }

    public function updateMyRequest(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string|max:255',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $leaveRequest = LeaveRequest::where('id', $request->requestId)
            ->where('user_id', Auth::id())
            ->where('answer', 'pending')
            ->firstOrFail();

        $leaveRequest->leave_type = $request->leave_type;
        $leaveRequest->start_date = $request->start_date;
        $leaveRequest->end_date = $request->end_date;
        $leaveRequest->save();

        return redirect()->route('employee.edit-my-requests')->with('success', 'Leave request updated successfully.');
    }
}
