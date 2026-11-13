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
                            <span class="badge bg-warning">{{ Auth::user()->applicant_branch() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')

    @if(!empty($reason))
        <div class="container-xl px-4 mt-n10">
            <div class="row">
                <div class="col-lg-8 col-xl-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            {{ $reason }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


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
