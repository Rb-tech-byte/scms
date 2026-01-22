@extends('layouts.app')
@section('title', $title ?? 'Course Assignments')
@section('header')
@php
if(auth()->user()->hasRole(['branch-admin','system-admin'])){
    $actionTitle = 'Assign Course';
}else{
    $actionTitle = null;
}

@endphp

    {!! $generatePageHeader(
        $title ?? 'Course Assignments',
        'course-assignments.index',
        'book-open',
        'Manage Course Assignment',
        'book-open',
        'course-assignments.create',
        'plus',
        $actionTitle,
        $variable ?? null,
    ) !!}
@endsection
@section('content')
    <div class="row">

        <div class="card">
            <div class="card-body">

                <table class="datatable table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-left">
                                @lang('crud.course_assignments.inputs.tutor_id')
                            </th>
                            <th class="text-left">
                               Branch
                            </th>
                            <th class="text-left">
                                @lang('crud.course_assignments.inputs.venue_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.course_assignments.inputs.course_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.course_assignments.inputs.date')
                            </th>
                            <th class="text-left">
                                @lang('crud.course_assignments.inputs.start_time')
                            </th>
                            <th class="text-left">
                                @lang('crud.course_assignments.inputs.end_time')
                            </th>
                            <th class="text-left">
                                Teached ?
                            </th>
                            @if(auth()->user()->hasRole(['branch-admin','system-admin']))
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courseAssignments as $courseAssignment)
                            <tr>
                                <td>
                                    <a href="{{route('tutors.show',$courseAssignment->tutor->id)}}">{{ optional($courseAssignment->tutor)->name ?? '-' }}</a>
                                </td>
                                <td>
                                    {{ optional($courseAssignment->branch)->name ?? '-' }}
                                </td>
                                <td>
                                    {{ optional($courseAssignment->venue)->name ?? '-' }}
                                </td>
                                <td>
                                    {{ optional($courseAssignment->course)->title ?? '-' }}
                                </td>
                                <td>{{ $courseAssignment->date->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $courseAssignment->start_time ?? '-' }}</td>
                                <td>{{ $courseAssignment->end_time ?? '-' }}</td>
                                <td> {!! active_or_disabled($courseAssignment->is_teached) ?? '-' !!}</td>
                                @if(auth()->user()->hasRole(['branch-admin','system-admin']))
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        @check('Update courseassignments')
                                            <a href="{{ route('course-assignments.edit', $courseAssignment) }}">
                                                <button type="button" class="btn btn-outline-primary m-1">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            
                                            @check('Delete courseassignments')
                                            
                                                 <button type="submit" class="btn btn-outline-danger m-1 deletebtn" link="{{ route('course-assignments.destroy', $courseAssignment) }}">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                        @endcheck
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
