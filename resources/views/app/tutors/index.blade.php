@extends('layouts.app')
@section('title', 'Tutor List')
@if(auth()->user()->hasRole(['admin', 'system-admin']))
    @php
     $addTutorText = 'Add Tutor';
     $addTutorLink = 'tutors.create';
     $manageTutorText = 'Manage Tutor';
      @endphp
@else
    @php
     $addTutorText = null;
        $addTutorLink = null;
        $manageTutorText = 'List Branch Tutors';
     @endphp
@endif

@section('header')
    {!! $generatePageHeader(
        'Tutors List',
        'tutors.index',
        'users',
        $manageTutorText,
        'users',
        $addTutorLink,
        'plus',
        $addTutorText
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
                                @lang('crud.tutors.inputs.name')
                            </th>
                            <th class="text-left">
                                @lang('crud.tutors.inputs.email')
                            </th>
                            <th class="text-left">
                                @lang('crud.tutors.inputs.phone')
                            </th>
                            <th class="text-left">
                                @lang('crud.tutors.inputs.address')
                            </th>
                            <th class="text-left">
                                @lang('crud.tutors.inputs.branch_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.tutors.inputs.active')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tutors as $tutor)
                            <tr>
                                <td>{{ $tutor->name ?? '-' }}</td>
                                <td>{{ $tutor->email ?? '-' }}</td>
                                <td>{{ $tutor->phone ?? '-' }}</td>
                                <td>{{ $tutor->address ?? '-' }}</td>
                                <td>{{ optional($tutor->branch)->name ?? '-' }}</td>
                                <td>{!! active_or_inactive($tutor->active) ?? '-' !!}</td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">

                                            @if (auth()->user()->hasRole(['admin', 'system-admin', 'branch-admin']) || auth()->user()->tutor->id == $tutor->id)
                                                <a href="{{ route('tutors.edit', $tutor) }}">
                                                    <button type="button" class="btn btn-outline-primary">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                </a>
                                            @endif


                                            <a href="{{ route('tutors.show', $tutor) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>

                                       
                                            @if (auth()->user()->hasRole(['admin', 'system-admin', 'branch-admin']))
                                                <button type="submit" class="btn btn-outline-danger deletebtn" link="{{ route('tutors.destroy', $tutor) }}">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                            @endif

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
    @endsection
