{{-- resources/views/admin/view-leave-reports.blade.php --}}

@extends('layouts.admin')

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
                <select class="form-control" id="leave_type" name="leave_type">
                    <option value="">Select leave type</option>
                    <option value="Pushim" {{ request('leave_type') == 'Pushim' ? 'selected' : '' }}>Pushim</option>
                    <option value="Flex" {{ request('leave_type') == 'Flex' ? 'selected' : '' }}>Flex</option>
                    <option value="Pushim mjeksor" {{ request('leave_type') == 'Pushim mjeksor' ? 'selected' : '' }}>Pushim mjeksor</option>
                    <option value="Tjeter" {{ request('leave_type') == 'Tjeter' ? 'selected' : '' }}>Tjeter</option>
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
            @foreach($requestsGrouped as $username => $requests)
                <tr onclick="toggleDetails('{{ $username }}')" class="cursor-pointer">
                    <td>{{ $username }} (click to toggle details)</td>
                    <td>{{ $requests->where('answer', 'approved')->sum(function ($req) {
                        return \Carbon\Carbon::parse($req->end_date)->diffInDays(\Carbon\Carbon::parse($req->start_date)) + 1;
                    }) }}</td>
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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td>{{ $request->leave_type }}</td>
                                        <td>{{ $request->start_date }}</td>
                                        <td>{{ $request->end_date }}</td>
                                        <td>{{ \Carbon\Carbon::parse($request->end_date)->diffInDays(\Carbon\Carbon::parse($request->start_date)) + 1 }}</td>
                                        <td>{{ $request->answer }}</td>
                                        <td>
                                            @if($request->answer === 'pending')
                                                <form method="POST" action="{{ route('admin.update-leave-request') }}" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="requestId" value="{{ $request->requestId }}">
                                                    <button type="submit" class="btn btn-success btn-sm" name="action" value="approve">Approve</button>
                                                    <button type="submit" class="btn btn-danger btn-sm" name="action" value="deny">Deny</button>
                                                </form>
                                            @endif
                                        </td>
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
