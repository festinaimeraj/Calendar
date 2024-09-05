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
                                        <button class="btn btn-primary edit-request-btn" 
                                            data-id="{{ $request->id }}"
                                            data-leave_type="{{ $request->leave_type }}"
                                            data-start_date="{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}" 
                                            data-end_date="{{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editLeaveModal">
                                            Edit
                                        </button>
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

<!-- Modal -->
<div class="modal fade" id="editLeaveModal" tabindex="-1" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editLeaveModalLabel">Edit Leave Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('employee.update-my-request') }}" id="editLeaveForm">
          @csrf
          <input type="hidden" name="requestId" id="requestId">

          <div class="form-group mb-3">
            <label for="leave_type" class="form-label">Leave Type:</label>
            <select id="leave_type" name="leave_type" class="form-select" required>
              @foreach($leaveTypes as $leaveType) 
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

          <button type="submit" class="btn btn-primary btn-block w-100">Update Request</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const requestId = this.getAttribute('data-id');
                const url = `{{ route('employee.edit-my-requests') }}?id=${requestId}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.editRequest) {
                            document.getElementById('requestId').value = data.editRequest.id;
                            document.getElementById('leave_type').value = data.editRequest.leave_type;
                            document.getElementById('start_date').value = data.editRequest.start_date;
                            document.getElementById('end_date').value = data.editRequest.end_date;
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error));
            });
        });

        $(document).ready(function() {
        $('#start_date, #end_date').datepicker({
            dateFormat: 'dd/mm/yy',
            beforeShowDay: $.datepicker.noWeekends
        });

        $('.edit-request-btn').on('click', function() {
            var requestId = $(this).data('id');
            var leaveType = $(this).data('leave_type');
            var startDate = $(this).data('start_date');
            var endDate = $(this).data('end_date');

            $('#requestId').val(requestId);
            $('#leave_type').val(leaveType);
            $('#start_date').val(startDate);
            $('#end_date').val(endDate);

            var startDateObj = $('#start_date').datepicker('getDate');
            $('#end_date').datepicker('option', 'minDate', startDateObj);
        });

        $('#start_date').on('change', function() {
            var startDate = $(this).datepicker('getDate');
            $('#end_date').datepicker('option', 'minDate', startDate);
            $('#end_date').datepicker('setDate', startDate);
        });
    });
});
</script>

@endsection
