<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class ApproveDenyRequestsController extends Controller
{
    // Show all pending leave requests
    public function index(){
        // Retrieve all leave requests with a status of 'pending'
        $requests = LeaveRequest::where('answer', 'pending')->get();
        return response()->json($requests);
    }

    // Approve a leave request
    public function approve(Request $request){
        // Validate the incoming request
        $request->validate([
            'id' => 'required',
            'response' => 'required|string'
        ]);

        // Find the leave request by ID
        $leaveRequest = LeaveRequest::find($request->id);

        if ($leaveRequest && $leaveRequest->answer === 'pending') {
            // Update the status to 'approved'
            $leaveRequest->answer = 'approved';
            $leaveRequest->save();

            return response()->json([
                "message" => "Leave request approved successfully",
                "request" => $leaveRequest
            ]);
        } else {
            return response()->json([
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }
//email
    public function deny(Request $request){
        $request->validate([
            'request_id' => 'required',
            'reason' => 'required|string'
        ]);

        // Find the leave request by ID
        $leaveRequest = LeaveRequest::find($request->request_id);

        if ($leaveRequest && $leaveRequest->answer === 'pending') {
            // Update the status to 'denied' and save the reason
            $leaveRequest->answer = 'denied';
            $leaveRequest->denial_reason = $request->input('reason'); // Make sure 'denial_reason' column exists
            $leaveRequest->save();

            return response()->json([
                "message" => "Leave request denied successfully",
                "request" => $leaveRequest
            ]);
        } else {
            return response()->json([
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }
}

