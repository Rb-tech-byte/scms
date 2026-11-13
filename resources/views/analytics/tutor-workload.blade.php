@extends('layouts.app')
@section('title', 'Instructor Workload')
@section('content')
<div class="container-xl px-4 mt-4">
    <h1 class="h3 mb-3">Instructor Teaching Workload</h1>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Instructor</th>
                        <th>Regional Centre</th>
                        <th class="text-center">Upcoming / Active Sessions</th>
                        <th class="text-center">Completed Sessions</th>
                        <th class="text-center">Total Assigned</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tutors as $t)
                        <tr>
                            <td class="fw-bold">{{ $t['name'] }}</td>
                            <td>{{ $t['branch'] }}</td>
                            <td class="text-center"><span class="badge bg-primary">{{ $t['active_sessions'] }}</span></td>
                            <td class="text-center"><span class="badge bg-success">{{ $t['completed_sessions'] }}</span></td>
                            <td class="text-center fw-bold">{{ $t['total_sessions'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No instructor workload records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
