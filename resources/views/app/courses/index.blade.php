@extends('layouts.app')
@section('title', 'Courses List')
@section('header')
    {!! $generatePageHeader(
        'Courses List',
        'courses.index',
        'book-open',
        'Manage courses',
        'book-open',
        'courses.create',
        'plus',
        'Add New Course',
    ) !!}
@endsection
@section('content')
    <div class="row">

        <div class="card">
            <div class="card-body">

                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th class="text-left">
                                @lang('crud.courses.inputs.title')
                            </th>
                            <th class="text-right">
                                @lang('crud.courses.inputs.fee')
                            </th>
                            <th class="text-left">
                                @lang('crud.courses.inputs.duration')
                            </th>
                            <th>
                                has discount ?
                            </th>
                            <th>
                                discount amount (tzs)
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>{{ $course->title ?? '-' }}</td>
                                <td>{{ number_format($course->fee, 2, '.', ',') ?? '-' }}</td>
                                <td>{{ getDurationClass($course->duration) ?? '-' }}</td>
                                <td>{!! return_boolean_badge($course->has_discount) !!}</td>
                                <td>
                                    @if ($course->has_discount)
                                        {{ number_format($course->discount, 2, '.', ',') ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        @check('Update courses')
                                            <a href="{{ route('courses.edit', $course) }}">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('View courses')
                                            <a href="{{ route('courses.show', $course) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('Delete courses')
                                         
                                                <button type="submit" class="btn btn-outline-danger deletebtn" link="{{ route('courses.destroy', $course) }}">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                        @endcheck
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
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
