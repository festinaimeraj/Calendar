<!-- resources/views/leave_types/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Leave Types</h1>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.leave_types.create') }}" class="btn btn-primary mb-3">Add New Leave Type</a>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    @if(auth()->user()->isAdmin())
                        <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($leaveTypes as $leaveType)
                    <tr>
                        <td>{{ $leaveType->id }}</td>
                        <td>{{ $leaveType->name }}</td>
                        <td>{{ $leaveType->description }}</td>
                        @if(auth()->user()->isAdmin())
                            <td>
                                <a href="{{ route('admin.leave_types.edit', $leaveType->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.leave_types.destroy', $leaveType->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
