@extends('errors.layout')
@section('page-title', __('403 Error - SCMS OUT'))
@section('image')
<img class="img-fluid p-4" src="{{asset('assets/assets/img/illustrations/404-error.svg')}}" alt="" />
@endsection
@section('message', __('This requested URL was not found on this server.'))
