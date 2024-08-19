<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class MyLeaveTotalsController extends Controller
{
    public function index(){
        $requests = LeaveRequest::all();
        return response()->json($requests);
    }

    public function showMyLeaveTotals($leaveType){
        $request = LeaveRequest::where('leave_type', $leaveType)->get();

        if(!empty($request)){
            return response()->json([
                "total_requests" => $request->count(),
                "requests" => $request
            ]);
        } else {
            return response()->json([
                "message" => "Leave requests not found for the specified type."
            ], 404);
        }
    }

    
}
