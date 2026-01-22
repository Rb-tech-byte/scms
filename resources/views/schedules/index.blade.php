@extends('layouts.app')
@section('title', 'Payment Schedules')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-calendar-alt text-primary"></i> Payment Schedules
            </h1>
            <p class="text-muted mb-0">Manage installment payment schedules for invoices</p>
        </div>
        <a href="{{ route('payment-schedules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate Schedule
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Schedules</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['pending'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['overdue'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['paid'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="span">&times;</span>
            </button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('payment-schedules.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Invoice</label>
                    <select name="invoice_id" class="form-control select2">
                        <option value="">All Invoices</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ request('invoice_id') == $invoice->id ? 'selected' : '' }}>
                                #{{ $invoice->id }} - {{ $invoice->applicant->full_name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('payment-schedules.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Schedules Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Payment Schedules
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Invoice</th>
                            <th>Applicant</th>
                            <th>Installment</th>
                            <th>Due Date</th>
                            <th>Amount (TZS)</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th>Paid At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->id }}</td>
                                <td>
                                    <a href="{{ route('payment-schedules.show', $schedule->invoice_id) }}" class="text-primary">
                                        #{{ $schedule->invoice_id }}
                                    </a>
                                </td>
                                <td>{{ $schedule->invoice->applicant->full_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        #{{ $schedule->installment_number }}
                                    </span>
                                </td>
                                <td>
                                    {{ $schedule->due_date->format('d M, Y') }}
                                    @if($schedule->isOverdue())
                                        <br><small class="text-danger">
                                            <i class="fas fa-exclamation-circle"></i> 
                                            {{ abs($schedule->days_until_due) }} days overdue
                                        </small>
                                    @elseif($schedule->isDueSoon())
                                        <br><small class="text-warning">
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
                                    {{ $schedule->sent_at ? $schedule->sent_at->format('d M, Y H:i') : '-' }}
                                </td>
                                <td>
                                    {{ $schedule->paid_at ? $schedule->paid_at->format('d M, Y H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($schedule->status === 'pending' || $schedule->status === 'overdue')
                                            <form action="{{ route('payment-schedules.mark-sent', $schedule) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-info" title="Mark as Sent">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($schedule->status !== 'paid' && $schedule->status !== 'cancelled')
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="cancelSchedule({{ $schedule->id }})" 
                                                    title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    No payment schedules found. Generate a schedule to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Cancel Schedule Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cancelForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Payment Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reason for Cancellation (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Enter reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}
</style>

@endsection

@section('scripts')
<script>
function cancelSchedule(scheduleId) {
    const form = document.getElementById('cancelForm');
    form.action = `/payment-schedules/${scheduleId}/cancel`;
    $('#cancelModal').modal('show');
}

// Initialize Select2
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
});
</script>
@endsection
