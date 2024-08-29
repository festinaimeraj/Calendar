@extends('layouts.app')

@section('title', 'Edit My Requests')

@section('content')

<div class="container">
    <h2 class="my-4 text-center">Pending Leave Requests</h2>
    
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
                                    <th>Answer</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaveRequests as $request)
                                    <tr>
                                        <td>{{ $request->id }}</td>
                                        <td>{{ $request->leave_type_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}</td> 
                                        <td>{{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}</td> 
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
    <div class="container d-flex justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4 text-center text-primary">Edit Leave Request</h2>
                
                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('employee.update-my-request') }}" method="POST" id="editLeaveForm">
                    @csrf
                    <input type="hidden" name="requestId" value="{{ $editRequest->id }}">

                    <div class="form-group mb-3">
                        <label for="start_date" class="form-label">Start Date:</label>
                        <input type="text" id="start_date" name="start_date" class="form-control" placeholder="dd/mm/yyyy" value="{{ $editRequest->start_date->format('d/m/Y') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="end_date" class="form-label">End Date:</label>
                        <input type="text" id="end_date" name="end_date" class="form-control" placeholder="dd/mm/yyyy" value="{{ $editRequest->end_date->format('d/m/Y') }}" required>
                    </div>


                    <button type="submit" class="btn btn-primary btn-block w-100">Update Request</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#start_date, #end_date').datepicker({
                dateFormat: 'dd/mm/yy',
                beforeShowDay: $.datepicker.noWeekends
            });

            $('#start_date').on('change', function() {
                var startDate = $(this).datepicker('getDate');
                $('#end_date').datepicker('option', 'minDate', startDate);
                $('#end_date').datepicker('setDate', startDate);
            });
        });
    </script>
@endif

@endsection
