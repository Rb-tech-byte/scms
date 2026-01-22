@extends('layouts.app')
@section('title', 'Edit enrollment')
@section('header')
    {!! $generatePageHeader(
        'Edit enrollment',
        'enrollments.index',
        'page',
        'Manage enrollments',
        'page',
        'enrollments.create',
        'plus',
        'Edit enrollment',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="PUT" action="{{ route('enrollments.update', $enrollment) }}" class="mt-4">
                    @include('app.enrollments.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('enrollments.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('enrollments.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add text-primary"></i>
                            @lang('crud.common.create')
                        </a>

                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.update')
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
@endsection
