@extends('layouts.app')

@section('title', 'Request Leave')



@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow-sm p-4">
            <h2 class="mb-4 text-center text-primary">Request Leave</h2>
            
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

            <form method="POST" action="{{ route('admin.request_leave.submit') }}" id="leaveForm">
                @csrf
                <div class="form-group mb-3">
                    <label for="leave_type" class="form-label">Leave Type:</label>
                    <select name="leave_type" id="leave_type" class="form-select" required>
                    @foreach ($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="text" id="start_date" name="start_date" class="form-control" placeholder="dd/mm/yyyy" required>
                </div>

                <div class="form-group mb-3">
                    <label for="end_date" class="form-label">End Date:</label>
                    <input type="text" id="end_date" name="end_date" class="form-control" placeholder="dd/mm/yyyy" required>
                </div>

                <div class="form-group mb-4">
                    <label for="reason" class="form-label">Reason:</label>
                    <textarea id="reason" name="reason" class="form-control" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block w-100">Submit Leave Request</button>
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
@endsection
