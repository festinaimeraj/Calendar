@extends('layouts.app')

@section('title', 'My Leave Totals')

@section('content')

<div class="container">
    <h2 class="my-4 text-center">My Leave Totals</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Leave Totals
                </div>
                <div class="card-body">
                    @if($leaveTotals->isEmpty())
                        <p>You have no approved leave days.</p>
                    @else
                    <ul class="list-group">
                    @foreach ($leaveTotals as $leave)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $leave->type->name }}
                                <span class="badge badge-primary badge-pill">{{ $leave->total_days }} days used</span>
                            </li>
                            <div class="mt-3 text-center">
                                @if (is_null($leave->type->max_days) || $leave->type->max_days == -1)
                                    <p>You have unlimited days left.</p>
                                @else
                                    <p>You have {{ $remainingDaysByType[$leave->leave_type] }} days left.</p>
                                @endif
                            </div>
                        @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
