@extends('layouts.app')

@section('title', 'Request Leave')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="col-md-6">
        <h2 class="my-4 text-center">Request Leave</h2>
        <form method="POST" action="{{ route('employee.request_leave.submit') }}" id="leaveForm">
            @csrf
            <div class="form-group">
                <label for="leave_type">Leave Type:</label>
                <select name="leave_type" id="leave_type" class="form-control" required>
                    @foreach ($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="text" id="start_date" name="start_date" class="form-control" placeholder="dd/mm/yyyy" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="text" id="end_date" name="end_date" class="form-control" placeholder="dd/mm/yyyy" required>
            </div>

            <div class="form-group">
                <label for="reason">Reason:</label>
                <textarea id="reason" name="reason" class="form-control" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit Leave Request</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#start_date, #end_date').datepicker({
            dateFormat: 'dd/mm/yy',
            beforeShowDay: $.datepicker.noWeekends
        });
    });
</script>
@endsection
