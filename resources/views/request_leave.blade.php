@extends('layouts.main')

@section('title','Request Leave')

@section('content')
<div class="container">
    <h2>Request Leave</h2>
    <form method="POST" action="{{ route('employee.request-leave.submit')}}" id="leaveForm">
        @csrf
        <label for="leave_type">Leave Type:</label>
        <select name="leave_type" id="leave_type" required>
            <option value="Pushim">Pushim</option>
            <option value="Flex">Flex</option>
            <option value="Pushim mjeksor">Pushim mjeksor</option>
            <option value="Tjeter">Tjeter</option>
        </select>

        <label for="start_date">Start Date:</label>
        <input type="text" id="start_date" name="start_date" placeholder="dd/mm/yyyy" required>

        <label for="end_date">End Date:</label>
        <input type="text" id="end_date" name="end_date" placeholder="dd/mm/yyyy" required>

        <label for="reason">Reason:</label>
        <textarea id="reason" name="reason" required></textarea>

        <input type="submit" value="Submmit Leave Request">
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    $(document).ready(function() {
        $('#start_date, #end_date').datepicker({
            dateFormat: 'dd/mm/yy',
            beforeShowDay: $.datepicker.noWeekends
        });
    });
</script>
@endsection
