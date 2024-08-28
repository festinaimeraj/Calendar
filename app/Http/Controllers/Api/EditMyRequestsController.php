<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class EditMyRequestsController extends Controller
{
    
    public function index() {
        $userId = Auth::id(); 
        $requests = LeaveRequest::where('user_id', $userId)
                                ->where('answer', 'pending')
                                ->get();

        return response()->json($requests);
    }

    
    public function show($requestId) {
        $userId = Auth::id(); 
        $request = LeaveRequest::where('id', $requestId)
                               ->where('user_id', $userId)
                               ->where('answer', 'pending')
                               ->first();

        if ($request) {
            return response()->json([
                "request" => $request,
                "status" => "success",
                'message' => 'Leave request retrieved successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
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
                'status' => 'success',
                "message" => "Leave request updated successfully."
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }
}
