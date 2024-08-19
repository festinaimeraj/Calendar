<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class RequestLeaveController extends Controller{

    public function index(){
        $requests = LeaveRequest::all();
        return response()->json($requests);
    }

    public function store(Request $request){

        $request = new LeaveRequest();
        $request->username = $request->input('username');
        $request->leave_type = $request->input('leave_type');
        $request->start_date = $request->input('start_date');
        $request->end_date = $request->input('end_date');
        $request->reason = $request->input('reason');
        $request->save();
        return response()->json([
            'message' => 'Request created successfully',
        ]);
    
    }
}






?>