@extends('layouts.app')
@section('title', 'enrollment')
@section('header')
    {!! $generatePageHeader(
        'enrollment',
        'enrollments.index',
        'page',
        'Manage enrollments',
        'page',
        'enrollments.create',
        'plus',
        'enrollment',
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
                                @lang('crud.enrollments.inputs.applicant_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.enrollments.inputs.course_id')
                            </th>
                            <th class="text-left">
                                Course Payment Status
                            </th>
                            <th class="text-left">
                                @lang('crud.enrollments.inputs.start_date')
                            </th>
                            <th class="text-left">
                                @lang('crud.enrollments.inputs.end_date')
                            </th>
                            <th class="text-right">
                                Course Fees (Tsh)
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td>
                                    {{ optional($enrollment->applicant)->full_name ?? '-' }}
                                </td>
                                <td>
                                    {{ optional($enrollment->course)->title ?? '-' }}
                                </td>
                                <td>
                                    {!! getStatusBadge($enrollment->invoices->status ?? '-') !!}
                                </td>
                                <td>{{ $enrollment->start_date->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $enrollment->end_date->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ number_format($enrollment->fee, 2, '.', ',') ?? '0' }}</td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        @check('Update enrollments')
                                            <a href="{{ route('enrollments.edit', $enrollment) }}">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('View enrollments')
                                            <a href="{{ route('enrollments.show', $enrollment) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('Delete enrollments')
                                          
                                                <button type="button" class="btn btn-outline-danger deletebtn" id="{{ $enrollment->id }}" link ='{{ route('enrollments.destroy',$enrollment) }}'>
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                        @endcheck
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
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
