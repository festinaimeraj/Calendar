
@extends('layouts.app')

@section('title', 'Edit Leave Type')

@section('content')
<div class="container">
    <h1>Edit Leave Type</h1>
    <form action="{{ route('admin.leave_types.update', $leaveType->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Leave Type Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ $leaveType->name }}" required>
        </div>

        <div>
            <label for="max_days">Maximum Days:</label>
            <input type="number" name="max_days" class="form-control" value="{{ $leaveType->max_days }}" min="0" required>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Update Leave Type</button>
    </form>
</div>
@endsection
