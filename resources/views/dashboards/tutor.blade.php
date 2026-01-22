@extends('layouts.app')
@section('header')
    <header class="page-header page-header-dark open-blue-mode  pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                            Dashboard | Welcome {{ Auth::user()->name }}
                        </h1>

                        <div class="small text-light" id="dashboard_time">
                            <span class="fw-500 text-info" id="dashboard_day"></span>
                            <span id="full_date"></span>
                            <span class="badge bg-warning">{{ Auth::user()->tutor->branch->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-4 mb-4">
            <a class="card lift h-100" href="{{ route('tutor.upcoming_session') }}">

                <div class="card-body d-flex justify-content-center flex-column">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="me-3">
                            <i class="feather-xl text-primary mb-3" data-feather="users"></i>
                            <h5>Upcoming sessions</h5>
                            <div class="text-primary font-weight-bold x-large">{{ count($sessions) }}</div>
                        </div>
                        <img src="{{ asset('assets/assets/img/illustrations/team-spirit.svg') }}" alt="..."
                            style="width: 8rem" />
                    </div>
                </div>

            </a>
        </div>
        <div class="col-xl-4 mb-4">
            <a class="card lift h-100" href="{{ route('tutor.completed_session') }}">
                <div class="card-body d-flex justify-content-center flex-column">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="me-3">
                            <i class="feather-xl text-secondary mb-3" data-feather="book"></i>
                            <h5>Total Sessions Teached</h5>
                            <div class="text-success font-weight-bold x-large">
                                {{ count($teached_sessions) }}</div>
                        </div>
                        <img src="{{ asset('assets/assets/img/illustrations/statistics.svg') }}" alt="..."
                            style="width: 8rem" />
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 mb-4">
            <a class="card lift h-100" href="{{ route('message.show_all') }}">
                <div class="card-body d-flex justify-content-center flex-column">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="me-3">
                            <i class="feather-xl text-green mb-3" data-feather="chat"></i>
                            <h5>New Messages</h5>
                            <div class="text-danger font-weight-bold x-large">
                                {{ $count_new_messages }}</div>
                        </div>
                        <img src="{{ asset('assets/assets/img/illustrations/browser-stats.svg') }}" alt="..."
                            style="width: 8rem" />
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
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
                    @forelse($sessions as $session)
                        <tr>
                            <td class="h6">{{ $session->course->title ?? '-' }}</td>
                            <td class="h6">{{ $session->branch->name ?? '-' }}</td>
                            <td class="h6">{{ $session->venue->name ?? '-' }}</td>
                            <td class="h6">{{ $session->date->format('d-m-Y') ?? '-' }}</td>
                            <td class="h6">{{ $session->start_time ?? '-' }}</td>
                            <td class="h6">{{ $session->end_time ?? '-' }}</td>
                            <td class="h6">
                                <a href="{{ route('update-teached.course', $session) }}"
                                    class="btn btn-sm btn-success">{{ $session->is_teached == true ? 'Teached' : 'Not Teached' }}</a>
                            </td>
                        </tr>
                    @empty
                        <span class="badge bg-purple">No courses assigned</span>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dashboardTime = document.getElementById('dashboard_time');
                const dashboardDay = document.getElementById('dashboard_day');
                const fullDate = document.getElementById('full_date');

                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
                    'October', 'November', 'December'
                ];

                function updateTime() {
                    const now = new Date();
                    const day = days[now.getDay()];
                    const month = months[now.getMonth()];
                    const date = now.getDate();
                    const year = now.getFullYear();
                    const hours = now.getHours();
                    const minutes = now.getMinutes();
                    const seconds = now.getSeconds();
                    const ampm = hours >= 12 ? 'PM' : 'AM';

                    dashboardDay.innerHTML = day;
                    fullDate.innerHTML = `· ${month} ${date}, ${year} · ${hours}:${minutes}:${seconds} ${ampm}`;
                }

                setInterval(updateTime, 1000);
            });
        </script>
    @endpush
    @push('chat_js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
    @endpush
@endsection
