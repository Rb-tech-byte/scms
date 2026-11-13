@extends('layouts.app')
@section('title', 'Sessions & Attendance')
@section('content')
<div class="container-xl px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Class Sessions & Student Attendance</h1>
        <a href="{{ route('attendance.export') }}" class="btn btn-outline-success btn-sm">
            <i class="fas fa-file-export me-1"></i> Export Attendance CSV
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('attendance.index') }}" class="row g-2 align-items-end">
                <div class="col-md-9">
                    <label class="form-label">Select Scheduled Class Session</label>
                    <select name="assignment_id" class="form-select" onchange="this.form.submit()">
                        @forelse($assignments as $assignment)
                            <option value="{{ $assignment->id }}" {{ $selectedAssignment && $selectedAssignment->id === $assignment->id ? 'selected' : '' }}>
                                {{ $assignment->course?->title ?? 'Course' }} — {{ $assignment->branch?->name ?? '' }} / {{ $assignment->venue?->name ?? '' }}
                                ({{ $assignment->date?->format('d-m-Y') }} {{ $assignment->start_time }}-{{ $assignment->end_time }})
                            </option>
                        @empty
                            <option value="">No scheduled sessions available</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">Load Roster</button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedAssignment)
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('attendance.store') }}">
                    @csrf
                    <input type="hidden" name="course_assignment_id" value="{{ $selectedAssignment->id }}">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Session Dates</th>
                                <th>Attendance %</th>
                                <th>Mark Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $enrollment)
                                @php $current = $existingAttendance[$enrollment->id] ?? 'present'; @endphp
                                <tr>
                                    <td>{{ $enrollment->applicant?->full_name ?? '-' }}</td>
                                    <td>{{ $enrollment->start_date?->format('d-m-Y') }} - {{ $enrollment->end_date?->format('d-m-Y') }}</td>
                                    <td>{{ number_format((float)$enrollment->attendance_percentage, 1) }}%</td>
                                    <td>
                                        <select name="attendance[{{ $enrollment->id }}]" class="form-select form-select-sm">
                                            <option value="present" {{ $current === 'present' ? 'selected' : '' }}>Present</option>
                                            <option value="absent" {{ $current === 'absent' ? 'selected' : '' }}>Absent</option>
                                            <option value="excused" {{ $current === 'excused' ? 'selected' : '' }}>Excused</option>
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No enrolled students found for this class venue.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($enrollments->isNotEmpty())
                        <button type="submit" class="btn btn-primary">Save Session Attendance</button>
                    @endif
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
