@extends('layouts.app')
@section('title', 'Payment Reminders')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-bell text-primary"></i> Payment Reminders
            </h1>
            <p class="text-muted mb-0">Send payment reminders to applicants with upcoming or overdue schedules</p>
        </div>
        <a href="{{ route('payment-schedules.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Schedules
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Filter Options --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filter Reminders
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('payment-schedules.reminders') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Days Ahead</label>
                    <select name="days_ahead" class="form-control">
                        <option value="0" {{ $daysAhead == 0 ? 'selected' : '' }}>Due Today Only</option>
                        <option value="3" {{ $daysAhead == 3 ? 'selected' : '' }}>Next 3 Days</option>
                        <option value="7" {{ $daysAhead == 7 ? 'selected' : '' }}>Next 7 Days</option>
                        <option value="14" {{ $daysAhead == 14 ? 'selected' : '' }}>Next 14 Days</option>
                        <option value="30" {{ $daysAhead == 30 ? 'selected' : '' }}>Next 30 Days</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Schedules Due for Reminder --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Schedules Due for Reminder ({{ $schedules->count() }})
            </h6>
            @if($schedules->count() > 0)
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary d-print-none" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Overdue Alerts
                    </button>
                    <button type="button" class="btn btn-sm btn-success d-print-none" onclick="sendAllReminders()">
                        <i class="fas fa-paper-plane"></i> Send All Reminders
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body">
            @if($schedules->count() > 0)
                <form id="remindersForm" method="POST" action="{{ route('payment-schedules.send-reminders') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Invoice</th>
                                    <th>Applicant</th>
                                    <th>Contact</th>
                                    <th>Installment</th>
                                    <th>Due Date</th>
                                    <th>Amount (TZS)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr class="{{ $schedule->isOverdue() ? 'table-danger' : ($schedule->isDueSoon(3) ? 'table-warning' : '') }}">
                                        <td>
                                            <input type="checkbox" name="schedule_ids[]" value="{{ $schedule->id }}" class="form-check-input schedule-checkbox">
                                        </td>
                                        <td>
                                            <a href="{{ route('payment-schedules.show', $schedule->invoice_id) }}" class="text-primary">
                                                #{{ $schedule->invoice_id }}
                                            </a>
                                        </td>
                                        <td>{{ $schedule->invoice->applicant->full_name ?? 'N/A' }}</td>
                                        <td>
                                            <small>
                                                <i class="fas fa-envelope"></i> {{ $schedule->invoice->applicant->email ?? 'N/A' }}<br>
                                                <i class="fas fa-phone"></i> {{ $schedule->invoice->applicant->mobile ?? 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                #{{ $schedule->installment_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $schedule->due_date->format('d M, Y') }}</strong>
                                            @if($schedule->isOverdue())
                                                <br><small class="text-danger">
                                                    <i class="fas fa-exclamation-circle"></i> 
                                                    {{ abs($schedule->days_until_due) }} days overdue
                                                </small>
                                            @else
                                                <br><small class="text-info">
                                                    <i class="fas fa-clock"></i> 
                                                    Due in {{ $schedule->days_until_due }} days
                                                </small>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">{{ number_format($schedule->amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $schedule->status_badge_class }}">
                                                {{ ucfirst($schedule->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('payment-schedules.mark-sent', $schedule) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-info" title="Send Reminder">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Send Selected Reminders
                        </button>
                        <small class="text-muted ml-3">
                            <span id="selectedCount">0</span> schedule(s) selected
                        </small>
                    </div>
                </form>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="text-muted">No Reminders to Send</h5>
                    <p class="text-muted">
                        There are no payment schedules due for reminders in the selected timeframe.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Information Card --}}
    <div class="card shadow mb-4 border-left-info">
        <div class="card-body">
            <h6 class="font-weight-bold text-info">
                <i class="fas fa-info-circle"></i> How Reminders Work
            </h6>
            <ul class="mb-0">
                <li>Reminders are sent to applicants with pending or overdue payment schedules</li>
                <li>Only schedules that haven't been marked as "sent" will appear in this list</li>
                <li>After sending, schedules are marked with a "sent_at" timestamp</li>
                <li>You can also run the command: <code>php artisan payments:send-reminders --days=7</code></li>
                <li>Configure email/SMS templates in the notification settings</li>
            </ul>
        </div>
    </div>
</div>

<style>
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#selectAll').change(function() {
        $('.schedule-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Individual checkbox
    $('.schedule-checkbox').change(function() {
        updateSelectedCount();
        
        // Update select all checkbox
        const total = $('.schedule-checkbox').length;
        const checked = $('.schedule-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
    });

    // Update selected count
    function updateSelectedCount() {
        const count = $('.schedule-checkbox:checked').length;
        $('#selectedCount').text(count);
    }

    // Initial count
    updateSelectedCount();
});

function sendAllReminders() {
    if (confirm('Are you sure you want to send reminders for all schedules?')) {
        $('.schedule-checkbox').prop('checked', true);
        $('#remindersForm').submit();
    }
}
</script>
@endsection
