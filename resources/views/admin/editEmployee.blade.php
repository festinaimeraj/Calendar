@extends('layouts.admin')

@section('title', 'Edit Employee')

@section('content')
<div class="container">
    <h1>Edit Employee</h1>
    <form method="POST" action="{{ route('admin.updateEmployee', $employee->id) }}">
        @csrf
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="{{ $employee->username }}" required>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $employee->name }}" required>
        </div>
        <div class="form-group">
            <label for="surname">Surname</label>
            <input type="text" class="form-control" id="surname" name="surname" value="{{ $employee->surname }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $employee->email }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Employee</button>
    </form>
</div>
@endsection
