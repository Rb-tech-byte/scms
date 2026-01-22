@extends('layouts.app')
@section('title', 'Show enrollment')
@section('header')
    {!! $generatePageHeader(
        'Show enrollment',
        'enrollments.index',
        'page',
        'Manage enrollments',
        'page',
        'enrollments.create',
        'plus',
        'Show enrollment',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.enrollments.inputs.applicant_id')</h5>
                        <span>{{ optional($enrollment->applicant)->full_name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.enrollments.inputs.course_id')</h5>
                        <span>{{ optional($enrollment->course)->title ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.enrollments.inputs.session')</h5>
                        <span>{{ $enrollment->session ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.enrollments.inputs.venue_id')</h5>
                        <span>{{ optional($enrollment->venue)->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.enrollments.inputs.start_date')</h5>
                        <span>{{ $enrollment->start_date ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.enrollments.inputs.end_date')</h5>
                        <span>{{ $enrollment->end_date ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.enrollments.inputs.fee')</h5>
                        <span>{{ number_format($enrollment->fee, 2, '.', ',') ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.enrollments.inputs.duration')</h5>
                        <span>{{ $enrollment->duration ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('enrollments.index') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @check('Add enrollments')
                        <a href="{{ route('enrollments.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcheck
                </div>
            </div>
        </div>
    </div>
@endsection
