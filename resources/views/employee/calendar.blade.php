@extends('layouts.app')

@section('title', 'Employee Calendar')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="admin-calendar">
    <h1>Employee Calendar</h1>
    <div id="calendar"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var isAdmin = false; 
        
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
            editable: isAdmin, 
            selectable: true, 
            eventResizableFromEnd: isAdmin,
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
                if (!confirm("Are you sure you want to delete this event?")) return;

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
        });

        calendar.render();
    });
</script>
@endsection
