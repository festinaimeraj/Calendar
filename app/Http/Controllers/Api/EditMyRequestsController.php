<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class EditMyRequestsController extends Controller
{
    // Retrieve all pending leave requests for the logged-in user
    public function index() {
        $userId = Auth::id(); // Get the currently authenticated user ID
        $requests = LeaveRequest::where('user_id', $userId)
                                ->where('answer', 'pending')
                                ->get();

        return response()->json($requests);
    }

    // Retrieve a specific pending leave request for the logged-in user
    public function show($requestId) {
        $userId = Auth::id(); // Get the currently authenticated user ID
        $request = LeaveRequest::where('id', $requestId)
                               ->where('user_id', $userId)
                               ->where('answer', 'pending')
                               ->first();

        if ($request) {
            return response()->json($request);
        } else {
            return response()->json([
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }

    // Update a specific leave request
    public function update(Request $request, $requestId) {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $userId = Auth::id(); // Get the currently authenticated user ID

        $leaveRequest = LeaveRequest::where('id', $requestId)
                                    ->where('user_id', $userId)
                                    ->where('answer', 'pending')
                                    ->first();

        if ($leaveRequest) {
            $leaveRequest->start_date = $request->start_date;
            $leaveRequest->end_date = $request->end_date;
            $leaveRequest->save();

            return response()->json([
                "message" => "Leave request updated successfully."
            ]);
        } else {
            return response()->json([
                "message" => "Leave request not found or not pending."
            ], 404);
        }
    }
}
