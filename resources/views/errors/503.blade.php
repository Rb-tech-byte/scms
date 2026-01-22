@extends('errors.layout')
@section('page-title', __('403 Error - SCMS OUT'))
@section('image')
<img class="img-fluid p-4" src="{{asset('assets/assets/img/illustrations/503-error-service-unavailable.svg')}}" alt="" />
@endsection
@section('message', __('The server is temporarily unable to service your request due to maintenance downtime or capacity problems. Please try again later.'))
