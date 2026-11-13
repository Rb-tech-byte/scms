@extends('layouts.app')
@section('title', 'Active Classes')
@section('content')
<div class="container-xl px-4 mt-4">
    <h1 class="h3 mb-3">Active Short Course Classes</h1>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Teaching Method</th>
                        <th>Regional Centre</th>
                        <th>Venue</th>
                        <th>Instructor</th>
                        <th>Schedule Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $cls)
                        <tr>
                            <td class="fw-bold">{{ $cls->course?->title ?? '-' }}</td>
                            <td>{{ $cls->course?->teachingMethodLabel() ?? 'Face to Face' }}</td>
                            <td>{{ $cls->branch?->name ?? '-' }}</td>
                            <td>{{ $cls->venue?->name ?? '-' }}</td>
                            <td>{{ $cls->tutor?->name ?? '-' }}</td>
                            <td>{{ $cls->date?->format('d-m-Y') ?? '-' }} ({{ $cls->start_time }} - {{ $cls->end_time }})</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No active classes scheduled.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

