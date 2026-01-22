@extends('layouts.app')
@section('title', 'Edit Course')
@section('header')
    {!! $generatePageHeader(
        'Edit Course',
        'courses.index',
        'book-open',
        'Manage courses',
        'book-open',
        'courses.create',
        'plus',
        'Edit New Course',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="PUT" action="{{ route('courses.update', $course) }}" class="mt-4">
                    @include('app.courses.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('courses.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('courses.create') }}" class="btn btn-light">
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
