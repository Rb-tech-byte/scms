@extends('layouts.app')
@section('title', 'Dashboard')

@push('css')
<style>
    /* Modern Card Animations */
    .stat-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background: linear-gradient(135deg, var(--card-gradient-start), var(--card-gradient-end));
        position: relative;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-card.card-students {
        --card-gradient-start: #013ba6;
        --card-gradient-end: #228ddd;
    }

    .stat-card.card-payments {
        --card-gradient-start: #012b7a;
        --card-gradient-end: #1a7bc2;
    }

    .stat-card.card-balance {
        --card-gradient-start: #228ddd;
        --card-gradient-end: #013ba6;
    }

    .stat-card.card-courses {
        --card-gradient-start: #1653c7;
        --card-gradient-end: #52a8e8;
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: white;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: white;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        margin: 0;
        line-height: 1;
    }

    .stat-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 8px;
    }

    .stat-trend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 12px;
    }

    /* Modern Chart Card */
    .chart-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .chart-card .card-header {
        background: linear-gradient(135deg, #013ba6 0%, #228ddd 100%);
        color: white;
        border: none;
        border-radius: 16px 16px 0 0;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
    }

    /* Table Enhancements */
    .modern-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .modern-table thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        padding: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #495057;
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f3f5;
    }

    .modern-table tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .modern-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Animated Header */
    .dashboard-header {
        background: linear-gradient(135deg, #013ba6 0%, #228ddd 100%);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .dashboard-time {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 8px 16px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 8px;
    }

    /* Responsive Grid */
    @media (max-width: 768px) {
        .stat-value {
            font-size: 2rem;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            font-size: 28px;
        }
    }

    /* Loading Animation */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s ease-in-out infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }
</style>
@endpush

@section('header')
    <header class="page-header page-header-dark dashboard-header pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                            Dashboard
                        </h1>
                        <div class="dashboard-time" id="dashboard_time">
                            <span class="fw-600" id="dashboard_day"></span>
                            <span id="full_date"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        {{-- Students Card --}}
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card card-students h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="stat-label">Total Students</div>
                            <h2 class="stat-value">{{ number_format(count($students)) }}</h2>
                            <div class="stat-trend">
                                <i data-feather="trending-up" style="width: 16px; height: 16px;"></i>
                                <span>Active</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payments Card --}}
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card card-payments h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="stat-label">Total Collections</div>
                            <h2 class="stat-value">{{ number_format($total_payments / 1000000, 2) }}M</h2>
                            <div class="stat-trend">
                                <i data-feather="arrow-up" style="width: 16px; height: 16px;"></i>
                                <span>TZS {{ number_format($total_payments, 0) }}</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i data-feather="credit-card"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Balance Card --}}
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card card-balance h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="stat-label">Pending Balance</div>
                            <h2 class="stat-value">{{ number_format($total_balances / 1000000, 2) }}M</h2>
                            <div class="stat-trend">
                                <i data-feather="alert-circle" style="width: 16px; height: 16px;"></i>
                                <span>TZS {{ number_format($total_balances, 0) }}</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i data-feather="file-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Courses Card --}}
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card card-courses h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="stat-label">Active Courses</div>
                            <h2 class="stat-value">{{ count($courses) }}</h2>
                            <div class="stat-trend">
                                <i data-feather="book-open" style="width: 16px; height: 16px;"></i>
                                <span>Available</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i data-feather="book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Extended Dashboard Content --}}
    @include('dashboards.admin_extended')

@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Real-time clock
        const dashboardDay = document.getElementById('dashboard_day');
        const fullDate = document.getElementById('full_date');

        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        function updateTime() {
            const now = new Date();
            const day = days[now.getDay()];
            const month = months[now.getMonth()];
            const date = now.getDate();
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            dashboardDay.innerHTML = day;
            fullDate.innerHTML = `· ${month} ${date}, ${year} · ${hours}:${minutes}:${seconds}`;
        }

        updateTime();
        setInterval(updateTime, 1000);

        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush

@push('chat_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
@endpush
