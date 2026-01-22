@extends('layouts.app')
@section('title', 'Applicants Report')

@push('css')
<style>
    .filter-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .filter-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 16px 16px 0 0;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
    }

    .summary-card {
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--card-start), var(--card-end));
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .summary-card.card-students {
        --card-start: #667eea;
        --card-end: #764ba2;
    }

    .summary-card.card-revenue {
        --card-start: #f093fb;
        --card-end: #f5576c;
    }

    .summary-card.card-balance {
        --card-start: #4facfe;
        --card-end: #00f2fe;
    }

    .summary-card.card-rate {
        --card-start: #43e97b;
        --card-end: #38f9d7;
    }

    .summary-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .summary-label {
        font-size: 0.875rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .report-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .report-table thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        padding: 1rem 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #495057;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .report-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f3f5;
    }

    .report-table tbody tr:hover {
        background: #f8f9fa;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .report-table tbody td {
        padding: 0.875rem 0.75rem;
        vertical-align: middle;
        white-space: nowrap;
    }

    .export-btn-group .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.625rem 1.25rem;
        transition: all 0.3s ease;
    }

    .export-btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .filter-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .badge-status {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
    }
</style>
@endpush

@section('header')
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i data-feather="file-text"></i></div>
                        Applicants Report
                    </h1>
                    <div class="page-header-subtitle">Comprehensive reporting with advanced filters and export options</div>
                </div>
            </div>
        </div>
    </div>
</header>
@endsection

@section('content')
{{-- Summary Cards --}}
@if(isset($summary))
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card summary-card card-students h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="summary-label">Total Students</div>
                        <h2 class="summary-value">{{ number_format($summary['total_students']) }}</h2>
                    </div>
                    <i data-feather="users" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card summary-card card-revenue h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="summary-label">Total Revenue</div>
                        <h2 class="summary-value">{{ number_format($summary['total_revenue'] / 1000000, 2) }}M</h2>
                        <small style="opacity: 0.8;">TZS {{ number_format($summary['total_revenue']) }}</small>
                    </div>
                    <i data-feather="trending-up" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card summary-card card-balance h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="summary-label">Pending Balance</div>
                        <h2 class="summary-value">{{ number_format($summary['total_balance'] / 1000000, 2) }}M</h2>
                        <small style="opacity: 0.8;">TZS {{ number_format($summary['total_balance']) }}</small>
                    </div>
                    <i data-feather="alert-circle" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card summary-card card-rate h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="summary-label">Collection Rate</div>
                        <h2 class="summary-value">{{ number_format($summary['collection_rate'], 1) }}%</h2>
                        <small style="opacity: 0.8;">{{ $summary['total_enrollments'] }} Enrollments</small>
                    </div>
                    <i data-feather="percent" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Filter Card --}}
<div class="card filter-card">
    <div class="card-header">
        <i data-feather="filter" style="width: 18px; height: 18px;"></i>
        Advanced Filters & Export Options
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('applicant.report') }}" id="reportForm">
            <div class="filter-section">
                <div class="row g-3">
                    {{-- Course Filter --}}
                    <div class="col-md-3">
                        <label for="course" class="form-label">Course</label>
                        <select name="course" class="form-select select2" id="search-course">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Branch Filter --}}
                    <div class="col-md-3">
                        <label for="branch" class="form-label">Branch</label>
                        <select name="branch" class="form-select select2" id="search-branch">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Venue Filter --}}
                    <div class="col-md-3">
                        <label for="venue" class="form-label">Venue</label>
                        <select name="venue" class="form-select select2" id="search-venue">
                            <option value="">All Venues</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Payment Status Filter --}}
                    <div class="col-md-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select" id="payment_status">
                            <option value="">All Statuses</option>
                            <option value="1">Paid</option>
                            <option value="2">Partially Paid</option>
                            <option value="0">Unpaid</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input class="form-control" type="date" id="date_from" name="date_from" />
                    </div>

                    {{-- Date To --}}
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input class="form-control" type="date" id="date_to" name="date_to" />
                    </div>

                    {{-- Education Level --}}
                    <div class="col-md-3">
                        <label for="education_level" class="form-label">Education Level</label>
                        <select name="education_level" class="form-select" id="education_level">
                            <option value="">All Levels</option>
                            <option value="1">STD VII</option>
                            <option value="2">Form 4</option>
                            <option value="3">Form VI</option>
                            <option value="4">College</option>
                            <option value="5">University</option>
                            <option value="6">Other</option>
                        </select>
                    </div>

                    {{-- Applicant Name Search --}}
                    <div class="col-md-3">
                        <label for="applicant" class="form-label">Applicant Name</label>
                        <div class="input-group">
                            <input class="form-control" type="text" name="applicant" id="applicant"
                                placeholder="Search by name..." />
                            <span class="input-group-text">
                                <i data-feather="search" style="width: 16px; height: 16px;"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Report Type --}}
                    <div class="col-md-3">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select name="report_type" class="form-select" id="report_type">
                            <option value="detailed">Detailed Report</option>
                            <option value="summary">Summary Report</option>
                        </select>
                    </div>

                    {{-- Group By (for summary) --}}
                    <div class="col-md-3" id="group_by_container" style="display: none;">
                        <label for="group_by" class="form-label">Group By</label>
                        <select name="group_by" class="form-select" id="group_by">
                            <option value="">No Grouping</option>
                            <option value="course">Course</option>
                            <option value="branch">Branch</option>
                            <option value="venue">Venue</option>
                        </select>
                    </div>

                    {{-- Per Page --}}
                    <div class="col-md-2">
                        <label for="per_page" class="form-label">Per Page</label>
                        <select name="per_page" class="form-select" id="per_page">
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-3 export-btn-group">
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('applicant.report') }}" class="btn btn-secondary">
                        <i data-feather="refresh-cw" style="width: 16px; height: 16px;"></i>
                        Reset
                    </a>
                </div>
                <div class="col-md-auto">
                    <button type="submit" name="action" value="filter" class="btn btn-primary">
                        <i data-feather="filter" style="width: 16px; height: 16px;"></i>
                        Apply Filters
                    </button>
                </div>
                <div class="col-md-auto">
                    <button type="button" class="btn btn-success" id="export_excel">
                        <i data-feather="file-text" style="width: 16px; height: 16px;"></i>
                        Export Excel
                    </button>
                </div>
                <div class="col-md-auto">
                    <button type="button" class="btn btn-danger" id="export_pdf">
                        <i data-feather="file" style="width: 16px; height: 16px;"></i>
                        Export PDF
                    </button>
                </div>
                <div class="col-md-auto">
                    <button type="button" class="btn btn-info" id="export_csv">
                        <i data-feather="download" style="width: 16px; height: 16px;"></i>
                        Export CSV
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Report Table --}}
<div class="card filter-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i data-feather="list" style="width: 18px; height: 18px;"></i>
            Report Results ({{ $paginatedItems->total() }} records)
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table report-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Branch</th>
                        <th>Venue</th>
                        <th>Total Fee</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Invoice #</th>
                        <th>Receipt #</th>
                        <th>Discount</th>
                        <th>Installment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedItems as $record)
                        <tr>
                            <td>{{ $loop->iteration + ($paginatedItems->currentPage() - 1) * $paginatedItems->perPage() }}</td>
                            <td>
                                <a href="{{ route('applicants.show', $record['applicant_id']) }}" class="text-decoration-none fw-500">
                                    {{ $record['full_name'] }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-primary-soft text-primary">
                                    {{ $record['course_name'] }}
                                </span>
                            </td>
                            <td>{{ $record['branch_name'] }}</td>
                            <td>{{ $record['venue_name'] }}</td>
                            <td class="text-end fw-600">{{ number_format($record['total_fee'], 2) }}</td>
                            <td class="text-end text-success fw-600">{{ number_format($record['paid_amount'], 2) }}</td>
                            <td class="text-end text-danger fw-600">{{ number_format($record['balance'], 2) }}</td>
                            <td>
                                @if($record['invoice_status'] == 'Paid')
                                    <span class="badge badge-status bg-success">Paid</span>
                                @elseif($record['invoice_status'] == 'Partially Paid')
                                    <span class="badge badge-status bg-warning">Partial</span>
                                @else
                                    <span class="badge badge-status bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td><code>{{ $record['invoice_number'] }}</code></td>
                            <td><small>{{ $record['receipt_numbers'] ?: '-' }}</small></td>
                            <td class="text-end">
                                @if($record['has_discount'])
                                    <span class="text-danger fw-600">{{ number_format($record['discount_amount'], 2) }}</span>
                                    <br><small class="text-muted">{{ $record['discount_reason'] }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info-soft text-info">
                                    {{ $record['installment_type'] }}
                                </span>
                            </td>
                            <td><small>{{ $record['invoice_date'] }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center py-5">
                                <i data-feather="inbox" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
                                <p class="text-muted mb-0">No records found. Try adjusting your filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($paginatedItems->hasPages())
    <div class="card-footer">
        {{ $paginatedItems->links() }}
    </div>
    @endif
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Show/hide group by based on report type
        $('#report_type').on('change', function() {
            if ($(this).val() === 'summary') {
                $('#group_by_container').show();
            } else {
                $('#group_by_container').hide();
            }
        });

        // Export handlers
        function exportReport(format) {
            let formData = $('#reportForm').serialize();
            let url = '';
            
            if (format === 'excel') {
                url = '{{ url("export-applicant-report-to-excel") }}';
            } else if (format === 'pdf') {
                url = '{{ url("export-applicant-report-to-pdf") }}';
            } else if (format === 'csv') {
                url = '{{ url("export-applicant-report-to-csv") }}';
            }
            
            window.location.href = url + '?' + formData;
        }

        $('#export_excel').click(function() {
            exportReport('excel');
        });

        $('#export_pdf').click(function() {
            exportReport('pdf');
        });

        $('#export_csv').click(function() {
            exportReport('csv');
        });

        // Prepopulate form from URL params
        function prepopulateForm() {
            let urlParams = new URLSearchParams(window.location.search);
            $('#search-course').val(urlParams.get('course')).trigger('change');
            $('#search-branch').val(urlParams.get('branch')).trigger('change');
            $('#search-venue').val(urlParams.get('venue')).trigger('change');
            $('#payment_status').val(urlParams.get('payment_status'));
            $('#education_level').val(urlParams.get('education_level'));
            $('#date_from').val(urlParams.get('date_from'));
            $('#date_to').val(urlParams.get('date_to'));
            $('#applicant').val(urlParams.get('applicant'));
            $('#report_type').val(urlParams.get('report_type') || 'detailed').trigger('change');
            $('#group_by').val(urlParams.get('group_by'));
            $('#per_page').val(urlParams.get('per_page') || '20');
        }

        prepopulateForm();

        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush