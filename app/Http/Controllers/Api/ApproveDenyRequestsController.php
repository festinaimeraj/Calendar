<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericMail;
use Carbon\Carbon;

class ApproveDenyRequestsController extends Controller
{
    public function index(){
        $requests = LeaveRequest::where('answer', 'pending')
        ->with('type:id,name')
        ->get();
        $formattedRequests = $requests->map(function($request) {
            return [
                'id' => $request->id,
                'name' => $request->user->name,
                'surname' => $request->user->surname,
                'leave_type' => $request->type->name, 
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'answer' => $request->answer,
            ];
        });
        return response()->json($formattedRequests);
    }

    
    public function approve(Request $request){
        
        $validateRequest = Validator::make($request->all(),
            [
                'id' => 'required',
            'response_message' => 'required|string'
            ]);

            if($validateRequest->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateRequest->errors()
                ], 401);
            }
        
        $leaveRequest = LeaveRequest::with('type:id,name')
                                    ->find($request->id);

        if ($leaveRequest && $leaveRequest->answer === 'pending') {
            
            $leaveRequest->answer = 'approved';
            $leaveRequest->save();

            $user = $leaveRequest->user;
            $email = $user->email;
            $fullName = $user->name . ' ' . $user->surname;
            $leaveType = $leaveRequest->type->name;
            $startDate = Carbon::parse($leaveRequest->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($leaveRequest->end_date)->format('Y-m-d');

            // Email content
            $subject = 'Leave Request Approved';
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
                            <h2 class='email-title'>Leave Request Approved</h2>
                        </div>
                        <div class='email-body'>
                            <p>Dear {$fullName},</p>
                            <p>Your leave request for <strong>{$leaveType}</strong> has been <strong>approved</strong>.</p>
                            <p><strong>Start Date:</strong> {$startDate}</p>
                            <p><strong>End Date:</strong> {$endDate}</p>
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
                "message" => "Leave request approved successfully",
                "request" => [
                    'id' => $leaveRequest->id,
                    'name' => $leaveRequest->user->name,
                    'surname' => $leaveRequest->user->surname,
                    'leave_type' => $leaveRequest->type->name, 
                    'start_date' => $leaveRequest->start_date,
                    'end_date' => $leaveRequest->end_date,
                    'reason' => $leaveRequest->reason,
                    'answer' => $leaveRequest->answer,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }

    public function deny(Request $request){
        $validateRequest = Validator::make($request->all(),
        [
            'id' => 'required',
            'response_message' => 'required|string'
        ]);

        if($validateRequest->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateRequest->errors()
            ], 401);
        }
    
        
        $leaveRequest = LeaveRequest::with('type:id,name') 
                                    ->find($request->id);

        if ($leaveRequest && $leaveRequest->answer === 'pending') {
            
            $leaveRequest->answer = 'denied';
            $leaveRequest->response_message = $request->input('response_message'); 
            $leaveRequest->save();

            $user = $leaveRequest->user;
            $email = $user->email;
            $fullName = $user->name . ' ' . $user->surname;
            $leaveType = $leaveRequest->type->name;
            $startDate = Carbon::parse($leaveRequest->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($leaveRequest->end_date)->format('Y-m-d');

          
            $subject = 'Leave Request Denied';
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
                            <h2 class='email-title'>Leave Request Denied</h2>
                        </div>
                        <div class='email-body'>
                            <p>Dear {$fullName},</p>
                            <p>Your leave request for <strong>{$leaveType}</strong> has been <strong>denied</strong>.</p>
                            <p><strong>Start Date:</strong> {$startDate}</p>
                            <p><strong>End Date:</strong> {$endDate}</p>
                            <p><strong>Reason:</strong> {$leaveRequest->response_message}</p>
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
                "message" => "Leave request denied successfully",
                "request" => [
                    'id' => $leaveRequest->id,
                    'name' => $leaveRequest->user->name,
                    'surname' => $leaveRequest->user->surname,
                    'leave_type' => $leaveRequest->type->name, 
                    'start_date' => $leaveRequest->start_date,
                    'end_date' => $leaveRequest->end_date,
                    'reason' => $leaveRequest->reason,
                    'answer' => $leaveRequest->answer,
                    'response_message' => $leaveRequest->response_message,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }
}

