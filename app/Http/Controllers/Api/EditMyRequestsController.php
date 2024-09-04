<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class EditMyRequestsController extends Controller
{
    
    public function index($requestId) {
        $userId = Auth::id(); 
        $requests = LeaveRequest::where('id', $requestId)
                                ->where('user_id', $userId)
                                ->where('answer', 'pending')
                                ->with('user:id,username') 
                                ->first();
        if ($requests) {
            return response()->json([
                "request" => [
                    'id' => $requests->id,
                    'username' => $requests->user->username,
                    'leave_type' => $requests->type->name,
                    'start_date' => $requests->start_date,
                    'end_date' => $requests->end_date,
                    'reason' => $requests->reason,
                    'answer' => $requests->answer,
                    
                ],
                "status" => true,
                'message' => 'Leave request retrieved successfully.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }


    public function update(Request $request, $requestId) {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $userId = Auth::id(); 

        $leaveRequest = LeaveRequest::where('id', $requestId)
                                    ->where('user_id', $userId)
                                    ->where('answer', 'pending')
                                    ->first();

        if ($leaveRequest) {
            $leaveRequest->start_date = $request->start_date;
            $leaveRequest->end_date = $request->end_date;
            $leaveRequest->save();

            return response()->json([
                'status' => true,
                "message" => "Leave request updated successfully."
            ]);
        } else {
            return response()->json([
                'status' => false,
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }
}
