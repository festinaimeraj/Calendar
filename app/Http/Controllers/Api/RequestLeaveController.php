<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\LeaveType;

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

    $leaveRequest = new LeaveRequest();

    $leaveRequest->user_id = Auth::user()->id;
    $leaveRequest->leave_type = $leaveType->id;
    $leaveRequest->start_date = $request->start_date;
    $leaveRequest->end_date = $request->end_date;
    $leaveRequest->reason = $request->reason;
    $leaveRequest->save();

    return response()->json([
        'status' => true,
        'message' => 'Leave request created successfully.',
        'leave_type' => $leaveType->name
    ], 201);
}


    public function sendEmail(Request $request){
     
        $request->validate([
            'email' => 'required|email',
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Email sent successfully',
        ]);
    }
}






?>