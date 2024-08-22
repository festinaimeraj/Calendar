<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel')</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">   <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}"> <!-- Include custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" type="text/js" href="{{ asset('js/app.js') }}">
    <link rel="stylesheet" type="text/js" href="{{ asset('js\calendar.js') }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @can('isAdmin')
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.calendar') }}">Calendar</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.employees') }}">Employees</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.admins') }}">Admins</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.request_leave') }}">Request Leave</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.approve-deny-requests') }}">Approve/Deny Requests</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.view-leave-reports') }}">View Leave Reports</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.leave_types.index') }}">Leave Types</a></li>
                        @endcan
                        @can('isEmployee')
                            <li class="nav-item"><a class="nav-link" href="{{ route('employee.calendar') }}">Calendar</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('employee.request_leave') }}">Request Leave</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('employee.my-leave-totals') }}">My Leave Totals</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('employee.edit-my-requests') }}">Edit My Requests</a></li>
                        @endcan
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                     {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @vite('resources/js/app.js')
    
</body>
</html>
