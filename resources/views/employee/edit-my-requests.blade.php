@extends('layouts.app')

@section('title', 'Edit My Requests')

@section('content')

<div class="container">
    <h2 class="my-4 text-center">Pending Leave Requests</h2>
    
    <!-- Display flash message -->
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    Pending Leave Requests
                </div>
                <div class="card-body">
                    @if($leaveRequests->isEmpty())
                        <p>No pending leave requests.</p>
                    @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Leave Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>answer</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaveRequests as $request)
                                    <tr>
                                        <td>{{ $request->id }}</td>
                                        <td>{{ $request->leave_type_name }}</td>
                                        <td>{{ $request->start_date }}</td>
                                        <td>{{ $request->end_date }}</td>
                                        <td>{{ $request->answer }}</td>
                                        <td>
                                            <a href="{{ route('employee.edit-my-requests', ['id' => $request->id]) }}" class="btn btn-primary">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($editRequest))
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Edit Leave Request
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employee.edit-my-requests') }}" method="POST">
                            @csrf
                            <input type="hidden" name="requestId" value="{{ $editRequest->id }}">
                            <div class="form-group">
                                <label for="leave_type">Leave Type:</label>
                                <select name="leave_type" id="leave_type" class="form-control" required>
                                    <option value="Pushim" {{ $editRequest->leave_type == 'Pushim' ? 'selected' : '' }}>Pushim</option>
                                    <option value="Flex" {{ $editRequest->leave_type == 'Flex' ? 'selected' : '' }}>Flex</option>
                                    <option value="Pushim mjeksor" {{ $editRequest->leave_type == 'Pushim mjeksor' ? 'selected' : '' }}>Pushim mjeksor</option>
                                    <option value="Tjeter" {{ $editRequest->leave_type == 'Tjeter' ? 'selected' : '' }}>Tjeter</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $editRequest->start_date }}" required>
                            </div>
                            <div class="form-group">
                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $editRequest->end_date }}" required>
                            </div>
                            <button type="submit" class="btn btn-success">Update Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
