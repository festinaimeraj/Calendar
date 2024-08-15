@extends('layouts.admin')

@section('title', 'Add Admin')

@section('content')
    <div class="container">
        <h1>Add Admin</h1>
        <form action="{{ route('admin.storeAdmin') }}" method="POST">
            @csrf
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>   
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            <button type="submit" class="button add-button">Add Admin</button>
        </form>
    </div>
@endsection

