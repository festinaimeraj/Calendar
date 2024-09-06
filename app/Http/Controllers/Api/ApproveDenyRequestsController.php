<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Validator;
class ApproveDenyRequestsController extends Controller
{
    public function index(){
        $requests = LeaveRequest::where('answer', 'pending')
        ->with('type:id,name')
        ->get();
        $formattedRequests = $requests->map(function($request) {
            return [
                'id' => $request->id,
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

            return response()->json([
                'status' => true,
                "message" => "Leave request approved successfully",
                "request" => [
                    'id' => $leaveRequest->id,
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

            return response()->json([
                'status' => true,
                "message" => "Leave request denied successfully",
                "request" => [
                    'id' => $leaveRequest->id,
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

