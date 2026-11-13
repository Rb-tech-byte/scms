@extends('layouts.app')
@section('title', 'Show Tutor')
@section('header')
@if(auth()->user()->hasRole(['admin', 'system-admin']))
    @php
     $addTutorText = 'Add Tutor';
     $listTutorLink = 'tutors.index';
     $manageTutorText = 'Manage Tutors';
     $listTutorText = 'List Tutor';
      @endphp
@else
    @php
     $listTutorText = 'List Branch Tutor';
     @endphp
@endif
    {!! $generatePageHeader('Show Tutor', 'tutors.index', 'user', $listTutorText, 'user-check') !!}
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card text-center">
                <div class="card-body">
                    @php
                        $tutorAvatar = profileImageUrl(optional($tutor->user)->image);
                    @endphp
                    @if ($tutorAvatar)
                        <img src="{{ $tutorAvatar }}" class="rounded-circle"
                            alt="profile-image">
                    @else
                        <img src="{{ asset('mini.png') }}" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">
                    @endif

                    <h4 class="mb-0 mt-2">{{ $tutor->name ?? '-' }}</h4>
                    <div class="d-flex justify-content-center">
                        <p class="mx-2 text-muted font-14">
                                <span class="badge bg-purple">{{ $tutor->user->role_->name }}</span>
                        </p>
                        <p class="text-muted font-14">{!! active_or_disabled($tutor->active) ?? '-' !!}
                        </p>
                    </div>



                    <div style="display: flex; align-items: center; justify-content:center;">
                        <a href="{{ route('tutors.edit', $tutor) }}" class="btn mx-2 btn-success btn-sm mb-2">Edit</a>

                        @check('Delete tutors')
                            
                                <button type="submit" class="btn btn-danger btn-sm mb-2 deletebtn" link="{{ route('tutors.destroy', $tutor) }}">
                                    <i class="fa-solid fa-trash-alt"></i>
                                </button>
                        @endcheck
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card -->



        </div> <!-- end col-->

        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                        <li class="nav-item">
                            <a href="#aboutme" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0 active">
                                PROFILE
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#courseAssignment" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                                COURSES SCHEDULES
                            </a>
                        </li>
                        @if(auth()->user()->hasRole(['admin', 'system-admin']))
                        <li class="nav-item">
                            <a href="#system_logs" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                                SYSTEM LOGS
                            </a>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="aboutme">

                            <h5 class="text-uppercase"><i class="mdi mdi-briefcase me-1"></i></h5>


                                <div class="container mt-4">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th class="h6">@lang('crud.tutors.inputs.name')</th>
                                                <td class="h6 text-small">{{ $tutor->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="h6">@lang('crud.tutors.inputs.email')</th>
                                                <td class="h6 text-small">{{ strtolower($tutor->email) ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="h6">Mobile: </th>
                                                <td class="h6 text-small">{{ $tutor->phone ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="h6">Home Address: </th>
                                                <td class="h6 text-small">{{ $tutor->address ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="h6">Branch</th>
                                                <td class="h6 text-small">{{ $tutor->branch->name ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        </div>

                        <div class="tab-pane show" id="courseAssignment">

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="h6">Course</th>
                                                <th class="h6">Branch</th>
                                                <th class="h6">Venue</th>
                                                <th class="h6">Schedule Date</th>
                                                <th class="h6">Session Start Time (24HRS)</th>
                                                <th class="h6">Session End time(24HRS)</th>
                                                <th class="h6">Schedule Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($courseAssignments as $courseAssignment)
                                                <tr>
                                                    <td class="h6">{{ $courseAssignment->course->title ?? '-' }}</td>
                                                    <td class="h6">{{ $courseAssignment->branch->name ?? '-' }}</td>
                                                    <td class="h6">{{ $courseAssignment->venue->name ?? '-' }}</td>
                                                    <td class="h6">{{ $courseAssignment->date->format('d-m-Y') ?? '-' }}</td>
                                                    <td class="h6">{{ $courseAssignment->start_time ?? '-' }}</td>
                                                    <td class="h6">{{ $courseAssignment->end_time ?? '-' }}</td>
                                                    <td class="h6">
                                                        <a href="{{ route('update-teached.course', $courseAssignment) }}"
                                                            class="btn btn-sm btn-success">{{$courseAssignment->is_teached==true ? 'Teached' :'Not Teached' }}</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <span class="badge bg-purple">No courses assigned</span>
                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        @if(auth()->user()->hasRole(['admin', 'system-admin']))
                        <div class="tab-content">
                            <div class="tab-pane" id="system_logs">

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="datatable">
                                            <thead>
                                                <tr>
                                                    <th class="h6">Created At</th>
                                                    <th class="h6">Updated At</th>
                                                    <th class="h6">Log event</th>
                                                    <th class="h6">Database Area</th>
                                                    <th class="h6">Data Changed Id</th>
                                                    <th class="h6">Properties</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($logs as $audit)
                                                    <tr>
                                                        <td class="h6">{{ $audit->created_at }}</td>
                                                        <td class="h6">{{ $audit->updated_at }}</td>
                                                        <td class="h6">{{ $audit->event }}</td>
                                                        <td class="h6">{{ $audit->subject_type }}</td>
                                                        <td class="h6">{{ $audit->subject_id }}</td>
                                                        <td class="h6">
                                                            {{ $audit->properties ?? '-' }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <span class="badge bg-purple">No logs found</span>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="tab-pane" id="settings">


                        </div>
                        <!-- end settings content-->

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
