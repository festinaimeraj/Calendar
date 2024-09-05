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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
        
    <script>
       document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var isAdmin = true; // Admin role
    var editEventModal = $('#editEventModal');
    var editEventForm = $('#editEventForm');
    var eventIdInput = $('#eventId');
    var startDateInput = $('#startDate');
    var endDateInput = $('#endDate');

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
                start_date: info.event.start.toISOString(),
                end_date: info.event.end ? info.event.end.toISOString() : null
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
                if (!data.status) {
                    info.revert();
                    alert(data.message || 'Failed to update event');
                }
            })
            .catch(error => {
    console.error('Error updating event:', error);
    info.revert();
    alert('Failed to update event. Please check the console for more details.');
});

        },
        eventResize: function(info) {
            if (!isAdmin) return;

            var eventData = {
                id: info.event.id,
                start_date: info.event.start.toISOString(),
                end_date: info.event.end ? info.event.end.toISOString() : null
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
                if (!data.status) {
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

            eventIdInput.val(info.event.id);
            startDateInput.val(info.event.startStr.substring(0, 10)); 
            endDateInput.val(info.event.endStr ? info.event.endStr.substring(0, 10) : '');

            $('#reason').val(info.event.extendedProps.reason || '');

            editEventModal.modal('show');
        }
            });

            calendar.render();

            $('#saveChanges').on('click', function() {
                var eventId = eventIdInput.val();
                var startDate = startDateInput.val();
                var endDate = endDateInput.val();

                fetch('/update_event', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: eventId,
                        start_date: startDate,
                        end_date: endDate
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.status) {
                        alert('Event updated successfully');
                        calendar.refetchEvents(); 
                        editEventModal.modal('hide');
                    } else {
                        alert('Failed to update event: ' + data.message);
                    }
                }).catch(error => console.error('Error:', error));
            });

            $('#deleteEvent').on('click', function() {
                var eventId = eventIdInput.val();

                if (confirm("Are you sure you want to delete this event?")) {
                    fetch('/delete_event', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ id: eventId })
                    }).then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            alert('Event deleted successfully');
                            calendar.refetchEvents(); 
                            editEventModal.modal('hide');
                        } else {
                            alert('Failed to delete event: ' + data.message);
                        }
                    }).catch(error => console.error('Error:', error));
                }
            });
            $('.modal-footer').on('hidden.bs.modal', function () {
                editEventForm[0].reset();
            });
        });

    
        </script>
    </div>


<div id="editEventModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Leave Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <div class="container d-flex justify-content-center">
                    <div class="col-md-12">
                        <div class="card shadow-sm p-4">
                <form id="editEventForm">
                    <input type="hidden" id="eventId" name="eventId">
                    <div class="form-group mb-3">
                        <label for="reason" class="form-label">Reason:</label>
                        <input type="text" id="reason" name="reason" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="startDate" class="form-label">Start Date:</label>
                        <input type="text" id="startDate" name="start_date" class="form-control" placeholder="yyyy-mm-dd" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="endDate" class="form-label">End Date:</label>
                        <input type="text" id="endDate" name="end_date" class="form-control" placeholder="yyyy-mm-dd" required>
                    </div>
                </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                <button type="button" class="btn btn-danger" id="deleteEvent">Delete Event</button>
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#start_date, #end_date').datepicker({
            dateFormat: 'dd/mm/yy',
            beforeShowDay: $.datepicker.noWeekends
        });

        $('#start_date').on('change', function() {
            var startDate = $(this).datepicker('getDate');
            $('#end_date').datepicker('option', 'minDate', startDate);
            $('#end_date').datepicker('setDate', startDate);
        });
    });
</script>
@endsection