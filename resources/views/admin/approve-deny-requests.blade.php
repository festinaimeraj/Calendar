@extends('layouts.app')

@section('title', 'Approve/Deny Leave Requests')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('content')
    <div class="container">
        <h1>Approve/Deny Leave Requests</h1>
        <div class="requests-container">
            @foreach ($leaveRequests as $request)
                <div class="request">
                    <div class="request-info">
                        <p><strong>User:</strong> {{ $request->name . ' '. $request->surname  }}</p>
                        <p><strong>Leave Type:</strong> {{ $request->leave_type_name }}</p>
                        <p><strong>Start Date:</strong> {{ $request->start_date }}</p>
                        <p><strong>End Date:</strong> {{ $request->end_date }}</p>
                        <p><strong>Reason:</strong> {{ $request->reason }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.processLeaveRequest') }}" class="request-form">
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $request->id }}">
                        <label for="response">Response:</label>
                        <textarea name="response" required></textarea>
                        <div class="form-buttons">
                            <input type="submit" name="action" value="approve" class="button approve-button">
                            <input type="submit" name="action" value="deny" class="button deny-button">
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
</head>
<body>
    <style>
        /* Container for all leave requests */
.requests-container {
    display: flex;
    flex-wrap: wrap;
    gap: 70px;
}

/* Each request card */
.request {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    width: 300px; /* Adjust width as needed */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

/* Request info section */
.request-info {
    margin-bottom: 20px;
}

/* Form buttons container */
.form-buttons {
    display: flex;
    gap: 10px;
}

/* Style for approve and deny buttons */
.button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    color: #fff;
}

.approve-button {
    background-color: #28a745; /* Green color for approve */
}

.deny-button {
    background-color: #dc3545; /* Red color for deny */
}

/* Style for textarea */
textarea {
    width: 100%;
    height: 60px;
    border-radius: 5px;
    border: 1px solid #ccc;
    padding: 10px;
}

    </style>
</body>
</html>