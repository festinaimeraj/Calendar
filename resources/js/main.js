// Import any additional JavaScript libraries or utilities here

// Import and initialize components if necessary
import './bootstrap'; // Ensure bootstrap is loaded

// Example: Initialize any additional libraries or plugins
import $ from 'jquery';
import 'bootstrap'; // Make sure Bootstrap JavaScript is included

// You can add custom JavaScript here for your application
$(document).ready(function() {
    // Custom initialization code
    console.log('main.js is loaded and ready.');

    // Example: Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// If you need to initialize Vue components or other libraries, you can do so here
