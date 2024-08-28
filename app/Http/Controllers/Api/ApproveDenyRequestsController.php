<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class ApproveDenyRequestsController extends Controller
{
    public function index(){
        $requests = LeaveRequest::where('answer', 'pending')->get();
        return response()->json($requests);
    }

    
    public function approve(Request $request){
        
        $request->validate([
            'id' => 'required',
            'response' => 'required|string'
        ]);

        
        $leaveRequest = LeaveRequest::find($request->id);

        if ($leaveRequest && $leaveRequest->answer === 'pending') {
            
            $leaveRequest->answer = 'approved';
            $leaveRequest->save();

            return response()->json([
                'status' => 200,
                "message" => "Leave request approved successfully",
                "request" => $leaveRequest
            ]);
        } else {
            return response()->json([
                'status' => 404,
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }

    public function deny(Request $request){
        $request->validate([
            'request_id' => 'required',
            'reason' => 'required|string'
        ]);

        
        $leaveRequest = LeaveRequest::find($request->request_id);

        if ($leaveRequest && $leaveRequest->answer === 'pending') {
            
            $leaveRequest->answer = 'denied';
            $leaveRequest->denial_reason = $request->input('reason'); 
            $leaveRequest->save();

            return response()->json([
                'status' => 200,
                "message" => "Leave request denied successfully",
                "request" => $leaveRequest
            ]);
        } else {
            return response()->json([
                'status' => 404,
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }
}

