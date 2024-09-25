<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class CalendarController extends Controller
{
    public function loadEvents()
    {
        $leaveTypeColors = [
            1 => '#f44336', 
            2 => '#ff9800',
            3 => '#4caf50', 
            4 => '#2196f3', 
            // Add more 
        ];
    
        if (auth()->user()->role == 'admin') {
            $leaveRequests = LeaveRequest::whereIn('answer', ['approved', 'pending'])->get();
        } else {
            $leaveRequests = LeaveRequest::where('answer', 'approved')->get();
        }
        $events = $leaveRequests->map(function ($request) use ($leaveTypeColors) {
            $leaveTypeId = $request->type->id;
    
                                $color = $leaveTypeColors[$leaveTypeId] ?? '#795548';
    
                                return [
                                    'id' => $request->id,
                                    'title' => $request->user->name.' '.$request->user->surname.'-'.$request->type->name,
                                    'start' => $request->start_date,
                                    'end' => $request->end_date,
                                    'answer' => $request->answer,
                                    'color' => $color,
                                ];
                            });
    
        return response()->json($events);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'leave_type' => 'required|integer|exists:leave_types,id'
            
        ]);

        $leaveRequest = LeaveRequest::find($validated['id']);
        if ($leaveRequest->end_date < now()->toDateString()) {
            return response()->json([
                'success' => false,
                'message' => 'Leave request cannot be edited after it has ended.'
            ], 403);
        }

        if ($leaveRequest) {
            $leaveRequest->start_date = $validated['start_date'];
            $leaveRequest->end_date = $validated['end_date'] ?? $leaveRequest->end_date;
            $leaveRequest->leave_type = $validated['leave_type']; 

            $leaveRequest->save();

            return response()->json(['success' => true, 'message' => 'Leave request updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Leave request not found.'], 404);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $leaveRequest = LeaveRequest::find($validated['id']);
        if ($leaveRequest) {
            $leaveRequest->delete();
            return response()->json(['success' => true, 'message' => 'Leave request deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Leave request not found.'], 404);
    }
}
