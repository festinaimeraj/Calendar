<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Event;

// class CalendarController extends Controller
// {
//     public function index()
//     {
//         $calendarType = Auth::user()->role == 'admin' ? 'Admin Calendar' : 'Employee Calendar';
//         $isAdmin = Auth::user()->role == 'admin';

//         return view('calendar', compact('calendarType', 'isAdmin'));
//     }

//     public function loadEvents()
//     {
//         // Load events from the database
//         $events = []; // Fetch events from the database and format them as needed
//         return response()->json($events);
//     }

//     public function updateEvent(Request $request)
//     {
//         // Update event in the database
//         $event = Event::find($request->id);
//         if ($event) {
//             $event->start = $request->start;
//             $event->end = $request->end;
//             $event->save();

//             return response()->json(['success' => true]);
//         }

//         return response()->json(['success' => false, 'message' => 'Event not found']);
//     }

//     public function deleteEvent(Request $request)
//     {
//         // Delete event from the database
//         $event = Event::find($request->id);
//         if ($event) {
//             $event->delete();

//             return response()->json(['success' => true]);
//         }

//         return response()->json(['success' => false, 'message' => 'Event not found']);
//     }
// }

// app/Http/Controllers/CalendarController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function showAdminCalendar()
    {
        return view('/admin/calendar'); // Adjust path if necessary
    }

    public function showEmployeeCalendar()
    {
        return view('/employee/calendar'); // Adjust path if necessary
    }
}
