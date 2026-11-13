@extends('errors.layout')
@section('page-title', __('403 Error - SCMS OUT'))
@section('image')
<img class="img-fluid p-4" src="{{asset('assets/assets/img/illustrations/403-error-forbidden.svg')}}" alt="" />
@endsection
@section('message')
    <div>
        <div>{{ __('You do not have permission to access this page.') }}</div>
        <div class="small text-muted mt-2">
            {{ __('If you believe this is a mistake, please contact your System Admin to update your role, permissions, or branch assignment.') }}
        </div>
    </div>
@endsection

@section('actions')
    <a class="btn btn-outline-secondary ms-2" href="{{ route('home') }}">{{ __('Go to Dashboard') }}</a>
@endsection
