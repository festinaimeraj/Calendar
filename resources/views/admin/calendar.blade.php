@extends('layouts.app')

@section('title', 'Admin Calendar')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/main.min.css">

    <div class="admin-calendar">
        <h1>Admin Calendar</h1>
        <div id="calendar"></div>
        
        <script src='node_modules/@fullcalendar/core/main.min.js'></script>
    <script src='node_modules/@fullcalendar/daygrid/main.min.js'></script>
    <script src='node_modules/@fullcalendar/timegrid/main.min.js'></script>
    <script src='node_modules/@fullcalendar/list/main.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
        
    <script>
       document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var isAdmin = true; // Admin role
        var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        displayEventTime: false,
        weekends: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '/load_events',
        editable: true,
        selectable: true,
        droppable: true,
        eventResizableFromStart: true, 
        eventStartEditable: true,
        eventDurationEditable: true,
        eventDrop: function(info) {

            var eventData = {
                id: info.event.id,
                start: info.event.start.toISOString(),
                end: info.event.end ? info.event.end.toISOString() : null
            };

            fetch('/update_event', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(eventData)
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    info.revert();
                    alert(data.message || 'Failed to update event');
                }
            })
            .catch(() => {
                info.revert();
                alert('Failed to update event');
            });
        },
        eventResize: function(info) {
            // if(!isAdmin) return;
            var eventData = {
                id: info.event.id,
                start: info.event.start.toISOString(),
                end: info.event.end ? info.event.end.toISOString() : null
            };

          updateEvent(eventData);

            fetch('/update_event', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(eventData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Event updated successfully:', data);
                } else {
                    alert(data.message || 'Failed to update event');
                    info.revert(); 
                }
            })
            .catch(error => {
                console.error('Error updating event:', error);
                alert('Failed to update event. Please try again.');
                info.revert(); 
            });
        },
        eventClick: function(info) {
            if (!isAdmin) return;

            if (confirm("Are you sure you want to delete this event?")) {
                var eventId = info.event.id;

                fetch('/delete_event', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id: eventId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        info.event.remove();
                        alert(data.message || 'Event deleted successfully');
                    } else {
                        alert(data.message || 'Failed to delete event');
                    }
                })
                .catch(() => {
                    alert('Failed to delete event');
                });
            }
        }
    });

    calendar.render();
});
        </script>
    </div>
@endsection