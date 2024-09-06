<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class EditMyRequestsController extends Controller
{
    
    public function index(Request $request, $requestId = null) {
        $userId = Auth::id(); 
    
        if ($requestId) {
            $leaveRequest = LeaveRequest::where('id', $requestId)
                                        ->where('user_id', $userId)
                                        ->where('answer', 'pending')
                                        ->with(['user:id,username', 'type:id,name'])
                                        ->first();
    
            if ($leaveRequest) {
                return response()->json([
                    "request" => [
                        'id' => $leaveRequest->id,
                        'username' => $leaveRequest->user->username,
                        'leave_type' => $leaveRequest->type->name,
                        'start_date' => $leaveRequest->start_date,
                        'end_date' => $leaveRequest->end_date,
                        'reason' => $leaveRequest->reason,
                        'answer' => $leaveRequest->answer,
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
        } else {
            $leaveRequests = LeaveRequest::where('user_id', $userId)
                                         ->where('answer', 'pending')
                                         ->with('type:id,name')
                                         ->get();
    
            $formattedRequests = $leaveRequests->map(function($leaveRequest) {
            return [
                'id' => $leaveRequest->id,
                'leave_type' => $leaveRequest->type->name, 
                'start_date' => $leaveRequest->start_date,
                'end_date' => $leaveRequest->end_date,
                'reason' => $leaveRequest->reason,
                'answer' => $leaveRequest->answer,
            ];
        });
            return response()->json($formattedRequests);
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
