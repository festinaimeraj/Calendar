<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class CalendarController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $requests = LeaveRequest::where('user_id', $userId)->where('answer', 'approved')->get();
        return response()->json($requests->map(function($request) {
            return [
                'id' => $request->id,
                'title' => $request->leave_type_name,
                'start' => $request->start_date->toIso8601String(),
                'end' => $request->end_date->toIso8601String(),
            ];
        }));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $leaveRequest = LeaveRequest::find($validated['id']);
        if ($leaveRequest) {
            $leaveRequest->start_date = $validated['start_date'];
            $leaveRequest->end_date = $validated['end_date'] ?? $leaveRequest->end_date;
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
