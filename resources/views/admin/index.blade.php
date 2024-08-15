@extends('admin.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Admin Dashboard') }}</div>

                <div class="card-body">
                    {{ __('You are logged in as an admin!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
