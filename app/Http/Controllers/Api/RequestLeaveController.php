<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class RequestLeaveController extends Controller{

    public function index(){
        $requests = LeaveRequest::all();
        return response()->json($requests);
    }

    
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'leave_type' => 'required|string',   // Ensure leave_type is required
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'reason' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Proceed with saving the leave request
    $leaveRequest = new LeaveRequest();

    $leaveRequest->user_id = Auth::user()->id;
    $leaveRequest->leave_type = $request->leave_type;
    $leaveRequest->start_date = $request->start_date;
    $leaveRequest->end_date = $request->end_date;
    $leaveRequest->reason = $request->reason;
    $leaveRequest->save();

    return response()->json(['message' => 'Leave request created successfully.'], 201);
}


    public function sendEmail(Request $request){
     
        $request->validate([
            'email' => 'required|email',
        ]);
        return response()->json([
            'message' => 'Email sent successfully',
        ]);
    }
}






?>