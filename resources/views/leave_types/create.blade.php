<!-- resources/views/leave_types/create.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Add New Leave Type</h1>

        <form action="{{ route('admin.leave_types.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="max_days">Maximum Days:</label>
                <input type="number" class="form-control" name="max_days" min="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
