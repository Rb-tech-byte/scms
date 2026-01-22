@extends('errors.layout')
@section('page-title', __('403 Error - SCMS OUT'))
@section('image')
<img class="img-fluid p-4" src="{{asset('assets/assets/img/illustrations/504-error-gateway-timeout.svg')}}" alt="" />
@endsection
@section('message', __('The server encountered a temporary error and could not complete your request.'))
