@extends('layouts.app')
@section('title', 'Certificates')
@section('content')
<div class="container-xl px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Completion Certificates</h1>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="ready" {{ $statusFilter === 'ready' ? 'selected' : '' }}>Ready Certificates</option>
                <option value="issued" {{ $statusFilter === 'issued' ? 'selected' : '' }}>Issued Certificates</option>
                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('certificates.notify_tutors') }}">
                @csrf
                @if(\App\Services\PermissionService::can('send-certificate-alerts') || \App\Services\PermissionService::can('issue-certificate'))
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Select ready certificates below or notify instructors for all pending/ready entries.</span>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-envelope me-1"></i> Notify Instructors Certificates Are Ready
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="36">Select</th>
                                <th>Serial #</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Centre</th>
                                <th>Status</th>
                                <th>Instructor Notified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($certificates as $cert)
                                <tr>
                                    <td><input type="checkbox" name="certificate_ids[]" value="{{ $cert->id }}"></td>
                                    <td><code>{{ $cert->serial_number }}</code></td>
                                    <td>{{ $cert->applicant?->full_name ?? '-' }}</td>
                                    <td>{{ $cert->course?->title ?? '-' }}</td>
                                    <td>{{ $cert->branch?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $cert->status === 'issued' ? 'success' : ($cert->status === 'ready' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($cert->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $cert->notified_at ? $cert->notified_at->format('d-m-Y H:i') : 'Not yet' }}</td>
                                    <td>
                                        <a href="{{ route('certificates.pdf', $cert) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                        <a href="{{ route('certificates.verify', ['serial' => $cert->serial_number]) }}" class="btn btn-sm btn-outline-info">
                                            Verify
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No eligible completion certificates found yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
