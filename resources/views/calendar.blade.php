@extends('layouts.app')

@section('title', 'Leave Calendar')

@section('content')
<div class="container">
    <h1>Leave Calendar</h1>
    <div id="calendar"></div>
   
    <script src='node_modules/@fullcalendar/core/main.min.js'></script>
    <script src='node_modules/@fullcalendar/daygrid/main.min.js'></script>
    <script src='node_modules/@fullcalendar/timegrid/main.min.js'></script>
    <script src='node_modules/@fullcalendar/list/main.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
        <script>console.log('Hello, World!');
       document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        weekends: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '/load_events',
        editable: true,
        selectable: true,
        eventDrop: function(info) {
            if (!isAdmin) return;

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
            if (!isAdmin) return;

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
                    alert(data.message || 'Failed to update event');
                }
            })
            .catch(() => {
                alert('Failed to update event');
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

<!-- @vite('resources/js/calendar.js') -->

