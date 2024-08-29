<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function loadEvents()
    {
        $events = LeaveRequest::where('answer', 'approved')
                            ->get()
                            ->map(function ($request) {
                                $color = '';
                                    switch ($request->type->name) {
                                        case 'Pushim':
                                            $color = '#f44336'; // Red
                                            break;
                                        case 'Flex':
                                            $color = '#ff9800'; // Orange
                                            break;
                                        case 'Pushim mjeksor':
                                            $color = '#4caf50'; // Blue
                                            break;
                                        case 'Tjeter':
                                            $color = '#4caf50'; // Green
                                            break;
                                        default:
                                            $color = '#795548'; // Brown
                                            break;
                                    }

                                return [
                                    'id' => $request->id,
                                    'title' => $request->user->name.' '.$request->user->surname. '-' .$request->type->name,
                                    'start' => $request->start_date,
                                    'end' => $request->end_date,
                                    'color' => $color,
                                ];
                            });

        return response()->json(['status' => true, 'events' => $events]);
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

            return response()->json(['status' => true, 'message' => 'Leave request updated successfully.']);
        }

        return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $leaveRequest = LeaveRequest::find($validated['id']);
        if ($leaveRequest) {
            $leaveRequest->delete();
            return response()->json(['status' => true, 'message' => 'Leave request deleted successfully.']);
        }

        return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
    }
}
