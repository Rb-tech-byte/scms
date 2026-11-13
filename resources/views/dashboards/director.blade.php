@extends('layouts.app')
@section('title', 'Director Dashboard')
@section('header')
    <header class="page-header page-header-dark open-blue-mode pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                            Director Overview | {{ Auth::user()->name }}
                        </h1>
                        <div class="page-header-subtitle">Active Classes, Regional Centres & Ready Completion Certificates</div>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="card lift h-100" href="{{ route('active.classes') }}">
                <div class="card-body">
                    <div class="small text-muted">Active Classes</div>
                    <div class="h2 text-primary mb-0">{{ $activeClassesCount }}</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="card lift h-100" href="{{ route('certificates.index', ['status' => 'ready']) }}">
                <div class="card-body">
                    <div class="small text-muted">Ready Certificates</div>
                    <div class="h2 text-success mb-0">{{ $readyCertificatesCount }}</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="card lift h-100" href="{{ route('certificates.index', ['status' => 'issued']) }}">
                <div class="card-body">
                    <div class="small text-muted">Issued Certificates</div>
                    <div class="h2 text-info mb-0">{{ $issuedCertificatesCount }}</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="card lift h-100" href="{{ route('reports.center_courses') }}">
                <div class="card-body">
                    <div class="small text-muted">Ongoing Enrollments</div>
                    <div class="h2 text-dark mb-0">{{ $ongoingEnrollmentsCount }}</div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-chalkboard-teacher me-1"></i> Active Classes</span>
                    <a href="{{ route('active.classes') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Centre</th>
                                <th>Instructor</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeClasses as $cls)
                                <tr>
                                    <td>{{ $cls->course?->title ?? '-' }}</td>
                                    <td>{{ $cls->branch?->name ?? '-' }}</td>
                                    <td>{{ $cls->tutor?->name ?? '-' }}</td>
                                    <td>{{ $cls->date?->format('d-m-Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">No active scheduled sessions.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-award me-1"></i> Ready Certificates</span>
                    <a href="{{ route('certificates.index', ['status' => 'ready']) }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Serial</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($readyCertificates as $cert)
                                <tr>
                                    <td><code>{{ $cert->serial_number }}</code></td>
                                    <td>{{ $cert->applicant?->full_name ?? '-' }}</td>
                                    <td>{{ $cert->course?->title ?? '-' }}</td>
                                    <td><span class="badge bg-{{ $cert->status === 'issued' ? 'success' : 'warning' }}">{{ ucfirst($cert->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">No ready certificates yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

