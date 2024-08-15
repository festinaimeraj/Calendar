import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import $ from 'jquery';

$(document).ready(function() {
    let calendarEl = document.getElementById('calendar');

    let calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        events: '/api/leave-requests', // Update this with the correct endpoint to fetch events
    });

    calendar.render();
});
