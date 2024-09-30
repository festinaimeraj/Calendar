<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericMail;

class RequestLeaveController extends Controller{

    public function index(){
        $requests = LeaveRequest::all();
        return response()->json($requests);
    }

    
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'leave_type' => 'required|string',   
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'reason' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
            'message' => 'Validation failed'
        ], 422);
    }

    $leaveType = LeaveType::where('name', $request->leave_type)->first();

    if (!$leaveType) {
        return response()->json([
            'status' => false,
            'message' => 'Leave type not found'
        ], 404);
    }

    $requestedDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;

    $totalApprovedDays = LeaveRequest::where('user_id', Auth::user()->id)
        ->where('leave_type', $leaveType->id)
        ->where('answer', 'approved')
        ->get()
        ->reduce(function ($carry, $item) {
            return $carry + $item->start_date->diffInDays($item->end_date) + 1;
        }, 0);

    if (($totalApprovedDays + $requestedDays) > $leaveType->max_days) {
        return response()->json([
            'status' => false,
            'message' => 'You cannot request more than ' . $leaveType->max_days . ' days for this leave type.'
        ], 400);
    }

    $leaveRequest = new LeaveRequest();
    $leaveRequest->user_id = Auth::user()->id;
    $leaveRequest->leave_type = $leaveType->id;
    $leaveRequest->start_date = $request->start_date;
    $leaveRequest->end_date = $request->end_date;
    $leaveRequest->reason = $request->reason;
    $leaveRequest->save();

    $user = Auth::user(); 
    $email = $user->email;
    $fullName = $user->name . ' ' . $user->surname;
    $leaveTypeName = $leaveType->name;
    $startDate = Carbon::parse($leaveRequest->start_date)->format('Y-m-d');
    $endDate = Carbon::parse($leaveRequest->end_date)->format('Y-m-d');

    $subject = 'Leave Request Submitted Successfully';
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
                    <h2 class='email-title'>Leave Request Submitted</h2>
                </div>
                <div class='email-body'>
                    <p>Dear {$fullName},</p>
                    <p>Your leave request for <strong>{$leaveTypeName}</strong> has been submitted successfully.</p>
                    <p><strong>Start Date:</strong> {$startDate}</p>
                    <p><strong>End Date:</strong> {$endDate}</p>
                    <p><strong>Reason:</strong> {$leaveRequest->reason}</p>
                </div>
                <div class='email-footer'>
                    <p>This is an automated message. Please do not reply.</p>
                </div>
            </div>
        </body>
    </html>
    ";

    Mail::to($email)->send(new GenericMail($subject, $body));
    return response()->json([
        'status' => true,
        'message' => 'Leave request created successfully.',
        'leave_type' => $leaveType->name
    ], 201);
}
}
?>