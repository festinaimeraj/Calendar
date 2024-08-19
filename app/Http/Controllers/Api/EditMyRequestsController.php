<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class EditMyRequestsController extends Controller
{
    public function index(){
        $requests = LeaveRequest::all();
        return response()->json($requests);
    }

    public function show($requestId) {
        $request = LeaveRequest::find($requestId);
        if(!empty($request)){
            return response()->json($request);
        } else {
            return response()->json([
                "message" => "Leave request not found."
            ], 404);
        }
    }

    public function update(Request $request, $requestId) {
        $request->validate([ 
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        if(LeaveRequest::where('requestId', $requestId)->exists()){
            $leaveRequest = LeaveRequest::find($requestId);
            $leaveRequest->start_date = $request->start_date;
            $leaveRequest->end_date = $request->end_date;
            $leaveRequest->save();
            return response()->json([
                "message" => "Leave request updated successfully."
            ]);
        } else {
            return response()->json([
                "message" => "Leave request not found."
            ], 404);
        }
    }
}
