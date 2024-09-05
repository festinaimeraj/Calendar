<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Validator;
class ApproveDenyRequestsController extends Controller
{
    public function index(){
        $requests = LeaveRequest::where('answer', 'pending')->get();
        return response()->json($requests);
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
        
        $leaveRequest = LeaveRequest::find($request->id);

        if ($leaveRequest && $leaveRequest->answer === 'pending') {
            
            $leaveRequest->answer = 'approved';
            $leaveRequest->save();

            return response()->json([
                'status' => true,
                "message" => "Leave request approved successfully",
                "request" => $leaveRequest
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
    
        
        $leaveRequest = LeaveRequest::find($request->id);

        if ($leaveRequest && $leaveRequest->answer === 'pending') {
            
            $leaveRequest->answer = 'denied';
            $leaveRequest->response_message = $request->input('response_message'); 
            $leaveRequest->save();

            return response()->json([
                'status' => true,
                "message" => "Leave request denied successfully",
                "request" => $leaveRequest
            ] , 200);
        } else {
            return response()->json([
                'status' => false,
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }
}

