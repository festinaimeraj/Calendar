<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('node_modules/@fullcalendar/core/main.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/@fullcalendar/daygrid/main.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/@fullcalendar/timegrid/main.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
    @include('partials.header')
    <div class="content">
        @yield('content')
    </div>
    <script src="{{ asset('node_modules/@fullcalendar/core/main.min.js') }}"></script>
    <script src="{{ asset('node_modules/@fullcalendar/daygrid/main.min.js') }}"></script>
    <script src="{{ asset('node_modules/@fullcalendar/timegrid/main.min.js') }}"></script>
    <script src="{{ asset('node_modules/@fullcalendar/list/main.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
</body>
</html>
