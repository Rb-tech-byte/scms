@extends('layouts.app')
@section('title', 'Regional Center Course Report')
@section('content')
<div class="container-xl px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Ongoing Regional Center Course Report</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.center_courses', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-sm btn-outline-success">
                <i class="fas fa-file-csv me-1"></i> CSV
            </a>
            <a href="{{ route('reports.center_courses', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.center_courses') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Reporting Period</label>
                    <select name="period" class="form-select">
                        <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly (Current Week)</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly (Current Month)</option>
                        <option value="quarterly" {{ $period === 'quarterly' ? 'selected' : '' }}>Quarterly (Current Quarter)</option>
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Time</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Regional Centre</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Centres</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Regional Centre</th>
                        <th>Course Name</th>
                        <th>Teaching Method</th>
                        <th class="text-center">Total Students</th>
                        <th class="text-center">Full Paid</th>
                        <th class="text-center">Half / Partial Paid</th>
                        <th class="text-center">Unpaid</th>
                        <th class="text-end">Collected (TZS)</th>
                        <th class="text-end">Outstanding (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ $row['branch_name'] }}</td>
                            <td>{{ $row['course_title'] }}</td>
                            <td>{{ $row['teaching_method'] }}</td>
                            <td class="text-center fw-bold">{{ $row['total_students'] }}</td>
                            <td class="text-center"><span class="badge bg-success">{{ $row['full_paid'] }}</span></td>
                            <td class="text-center"><span class="badge bg-warning text-dark">{{ $row['half_paid'] }}</span></td>
                            <td class="text-center"><span class="badge bg-danger">{{ $row['unpaid'] }}</span></td>
                            <td class="text-end">{{ number_format($row['collected'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['outstanding'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">No courses recorded for the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

