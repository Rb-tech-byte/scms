@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('course-assignments.index') }}" class="mr-4"><i class="icon ion-md-arrow-back"></i></a>
                    @lang('crud.course_assignments.show_title')
                </h4>

                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.course_assignments.inputs.tutor_id')</h5>
                        <span>{{ optional($courseAssignment->tutor)->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.course_assignments.inputs.branch_id')</h5>
                        <span>{{ optional($courseAssignment->branch)->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.course_assignments.inputs.venue_id')</h5>
                        <span>{{ optional($courseAssignment->venue)->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.course_assignments.inputs.course_id')</h5>
                        <span>{{ optional($courseAssignment->course)->title ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.course_assignments.inputs.date')</h5>
                        <span>{{ $courseAssignment->date ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.course_assignments.inputs.start_time')</h5>
                        <span>{{ $courseAssignment->start_time ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.course_assignments.inputs.end_time')</h5>
                        <span>{{ $courseAssignment->end_time ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.course_assignments.inputs.active')</h5>
                        <span>{{ $courseAssignment->active ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('course-assignments.index') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @check('Add courseassignments')
                        <a href="{{ route('course-assignments.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcheck
                </div>
            </div>
        </div>
    </div>
@endsection
