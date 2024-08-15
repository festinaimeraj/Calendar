@extends('layouts.app')

@section('title', 'Employee Calendar')

@section('content')
    <div class="employee-calendar">
        <h1>Employee Calendar</h1>
        <div id="calendar"></div>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
        <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: '/api/leave-requests', // URL to fetch leave requests
        });
        calendar.render();
    });
</script>
    </div>
@endsection
