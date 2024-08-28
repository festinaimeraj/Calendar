@extends('layouts.app')

@section('title', 'Leave Reports')

@section('content')
<div class="container">
    <h1 class="my-4">Leave Reports</h1>

    <form class="search-form mb-4" method="GET" action="{{ route('admin.view-leave-reports') }}">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="user">Search by User:</label>
                <input type="text" class="form-control" id="user" name="user" placeholder="Enter username" value="{{ request('user') }}">
            </div>
            <div class="form-group col-md-6">
                <label for="leave_type">Search by Leave Type:</label>
                <select class="form-control" id="leave_type" name="leave_type" placeholder="Enter leave type">
                @foreach ($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Search</button>
    </form>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Username</th>
            <th>Total Days Used</th>
        </tr>
    </thead>
    <tbody>
        @foreach($requestsGrouped as $username => $groupData)
            <tr onclick="toggleDetails('{{ $username }}')" class="cursor-pointer">
                <td>{{ $username }} (click to toggle details)</td>
                <td>
                    @foreach($groupData['total_days'] as $leaveType => $days)
                        <strong>{{ $leaveType }}:</strong> 
                         {{ $days['approved'] }} days
                        
                        <br>
                    @endforeach
                </td>
            </tr>
            <tr id="details-{{ $username }}" class="leave-details" style="display: none;">
                <td colspan="2">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Requested Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupData['requests'] as $request)
                            <tr>
                                <td>{{ $request['leave_type'] }}</td>
                                <td>{{ $request['start_date'] }}</td>
                                <td>{{ $request['end_date'] }}</td>
                                <td>{{ $request['requested_days'] }}</td>
                                <td>{{ $request['answer'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</div>

<script>
    function toggleDetails(username) {
        var details = document.getElementById('details-' + username);
        if (details.style.display === 'none' || details.style.display === '') {
            details.style.display = 'table-row';
        } else {
            details.style.display = 'none';
        }
    }
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .leave-details .nested-table {
        margin: 10px 0;
    }
</style>
@endsection
