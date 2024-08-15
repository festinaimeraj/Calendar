@extends('layouts.admin')

@section('title', 'Approve/Deny Leave Requests')

@section('content')
    <div class="container">
        <h1>Approve/Deny Leave Requests</h1>
        @foreach ($leaveRequests as $request)
            <div class="request">
                <div class="request-info">
                    <p><strong>Username:</strong> {{ $request->username }}</p>
                    <p><strong>Leave Type:</strong> {{ $request->leave_type }}</p>
                    <p><strong>Start Date:</strong> {{ $request->start_date }}</p>
                    <p><strong>End Date:</strong> {{ $request->end_date }}</p>
                    <p><strong>Reason:</strong> {{ $request->reason }}</p>
                </div>
                <form method="POST" action="{{ route('admin.processLeaveRequest') }}" class="request-form">
                    @csrf
                    <input type="hidden" name="request_id" value="{{ $request->requestId }}">
                    <label for="response">Response:</label>
                    <textarea name="response" required></textarea>
                    <input type="submit" name="action" value="approve" class="button approve-button">
                    <input type="submit" name="action" value="deny" class="button deny-button">
                </form>
            </div>
        @endforeach
    </div>
@endsection
