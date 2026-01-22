@extends('layouts.app')
@section('title', 'Show Course')
@section('header')
    {!! $generatePageHeader(
        'Show Courses',
        'courses.index',
        'book-open',
        'Manage courses',
        'book-open',
        'courses.create',
        'plus',
        'Show Course',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.courses.inputs.title')</h5>
                        <span>{{ $course->title ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.courses.inputs.fee')</h5>
                        <span>{{ number_format($course->fee, 2, '.', ',') ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.courses.inputs.duration')</h5>
                        <span>{{ getDurationClass($course->duration) ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('courses.index') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @check('Add courses')
                        <a href="{{ route('courses.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcheck
                </div>
            </div>
        </div>
    </div>
@endsection
