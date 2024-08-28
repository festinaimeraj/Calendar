<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;

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
                                            $color = '#4caf50'; // Green
                                            break;
                                        case 'Tjeter':
                                            $color = '#2196f3'; // Blue
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

        return response()->json($events);
    }
    
    public function updateEvent(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $event = LeaveRequest::find($request->id);
        if ($event) {
            $event->start_date = $request->start;
            $event->end_date = $request->end;
            $event->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Event not found']);
    }

    public function deleteEvent(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $event = LeaveRequest::find($request->id);
        if ($event) {
            $event->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Event not found']);
    }
}
