@extends('layouts.app')

@section('title', 'Manage Employees')

@section('content')

@if(auth()->user()->isAdmin())
            <a href="{{ route('admin.addEmployee') }}" class="btn btn-primary">Add Employee</a>
        @endif


    <div class="container">
        <h1>Manage Employees</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>Total Days Used</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
               
                    <tr>
                        <td>{{ $employee->id }}</td>
                        <td>{{ $employee->username }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->surname }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->total_days_used !== NULL ? $employee->total_days_used . '/18' : '0/18' }}</td>
                        <td>
                            <a href="{{ route('admin.editEmployee', $employee->id) }}" class="button">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No employees found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('styles')
<style>
    .container {
        max-width: 900px;
        margin: 40px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #007bff;
        color: #fff;
    }
    tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .button {
        padding: 10px 20px;
        margin: 5px;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: #fff;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .button:hover {
        background-color: #0056b3;
    }
    .add-button {
        background-color: #28a745;
    }
    .add-button:hover {
        background-color: #218838;
    }
</style>
@endpush

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')

