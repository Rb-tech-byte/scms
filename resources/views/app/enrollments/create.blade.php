@extends('layouts.app')
@section('title', 'Add enrollment')
@section('header')
    {!! $generatePageHeader(
        'Add enrollment',
        'enrollments.index',
        'page',
        'Manage enrollments',
        'page',
        'enrollments.create',
        'plus',
        'Add enrollment',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="POST" action="{{ route('enrollments.store') }}" class="mt-4">
                    @include('app.enrollments.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('enrollments.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.create')
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
@endsection
