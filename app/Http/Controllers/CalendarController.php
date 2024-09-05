<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function loadEvents()
{
    $leaveTypeColors = [
        1 => '#f44336', 
        2 => '#ff9800', 
        3 => '#4caf50', 
        4 => '#2196f3', 
        // Add more colors as needed
    ];

    $user = auth()->user(); 
    $isAdmin = $user->role === 'admin'; 

    $leaveRequests = $isAdmin
        ? LeaveRequest::all() 
        : LeaveRequest::where('user_id', $user->id)
                      ->orWhere('answer', 'approved')
                      ->get(); 

    $events = $leaveRequests->map(function ($request) use ($leaveTypeColors) {
        $leaveTypeId = $request->type->id; 
  
        $color = ($request->answer === 'approved') ? 
            ($leaveTypeColors[$leaveTypeId] ?? '#795548') : 
            '#9e9e9e';

        return [
            'id' => $request->id,
            'title' => $request->user->name . ' ' . $request->user->surname . ' - ' . $request->type->name,
            'start' => $request->start_date,
            'end' => $request->end_date,
            'color' => $color,
        ];
    });

    return response()->json($events);
}

    
    
    
    public function updateEvent(Request $request)
{
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
    }
    
    Log::info('Update Event Request:', $request->all());

    $validatedData = $request->validate([
        'id' => 'required|integer|exists:leave_requests,id',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date'
    ]);

    $event = LeaveRequest::find($validatedData['id']);
    if ($event) {
        $event->start_date = $validatedData['start_date'];
        $event->end_date = $validatedData['end_date'];
        $event->save();

        return response()->json(['status' => true]);
    }

    return response()->json(['status' => false, 'message' => 'Event not found']);
}


    public function deleteEvent(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $event = LeaveRequest::find($request->id);
        if ($event) {
            $event->delete();

            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false, 'message' => 'Event not found']);
    }
}
