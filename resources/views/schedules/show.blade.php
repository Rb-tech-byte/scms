@extends('layouts.app')
@section('title', 'Invoice Payment Schedule')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-calendar-alt text-primary"></i> Payment Schedule
            </h1>
            <p class="text-muted mb-0">Invoice #{{ $invoice->id }} - {{ $invoice->applicant->full_name ?? 'N/A' }}</p>
        </div>
        <div>
            <a href="{{ route('payment-schedules.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Generate New Schedule
            </a>
            <a href="{{ route('payment-schedules.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
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

    <div class="row">
        {{-- Invoice Details --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-file-invoice"></i> Invoice Information
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Invoice ID:</dt>
                        <dd class="col-sm-6">#{{ $invoice->id }}</dd>

                        <dt class="col-sm-6">Applicant:</dt>
                        <dd class="col-sm-6">{{ $invoice->applicant->full_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-6">Email:</dt>
                        <dd class="col-sm-6">{{ $invoice->applicant->email ?? 'N/A' }}</dd>

                        <dt class="col-sm-6">Mobile:</dt>
                        <dd class="col-sm-6">{{ $invoice->applicant->mobile ?? 'N/A' }}</dd>

                        <dt class="col-sm-6">Total Amount:</dt>
                        <dd class="col-sm-6 font-weight-bold">
                            TZS {{ number_format($invoice->balance + $invoice->paid_amount, 2) }}
                        </dd>

                        <dt class="col-sm-6">Paid Amount:</dt>
                        <dd class="col-sm-6 text-success">
                            TZS {{ number_format($invoice->paid_amount, 2) }}
                        </dd>

                        <dt class="col-sm-6">Balance:</dt>
                        <dd class="col-sm-6 text-danger font-weight-bold">
                            TZS {{ number_format($invoice->balance, 2) }}
                        </dd>

                        <dt class="col-sm-6">Status:</dt>
                        <dd class="col-sm-6">
                            <span class="badge badge-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Schedule Statistics --}}
        <div class="col-lg-8 mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Schedules</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $statistics['total_schedules'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $statistics['paid_schedules'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $statistics['pending_schedules'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $statistics['overdue_schedules'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Scheduled Amount</div>
                            <div class="h6 mb-0 font-weight-bold">TZS {{ number_format($statistics['total_scheduled_amount'], 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid via Schedule</div>
                            <div class="h6 mb-0 font-weight-bold">TZS {{ number_format($statistics['paid_amount'], 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Amount</div>
                            <div class="h6 mb-0 font-weight-bold">TZS {{ number_format($statistics['pending_amount'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Schedules Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Payment Installments
            </h6>
        </div>
        <div class="card-body">
            @if($invoice->paymentSchedules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Installment</th>
                                <th>Due Date</th>
                                <th>Amount (TZS)</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Paid At</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->paymentSchedules as $schedule)
                                <tr class="{{ $schedule->isOverdue() ? 'table-danger' : '' }}">
                                    <td>
                                        <span class="badge badge-info badge-lg">
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
                                        @if($schedule->sent_at)
                                            <small>{{ $schedule->sent_at->format('d M, Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($schedule->paid_at)
                                            <small class="text-success">
                                                <i class="fas fa-check-circle"></i>
                                                {{ $schedule->paid_at->format('d M, Y H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($schedule->notes)
                                            <small class="text-muted">{{ Str::limit($schedule->notes, 30) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
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

                                            <form action="{{ route('payment-schedules.destroy', $schedule) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="font-weight-bold">
                            <tr>
                                <td colspan="2" class="text-right">Total:</td>
                                <td>{{ number_format($invoice->paymentSchedules->sum('amount'), 2) }}</td>
                                <td colspan="5"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Payment Schedules Found</h5>
                    <p class="text-muted">Generate a payment schedule to create installment plans for this invoice.</p>
                    <a href="{{ route('payment-schedules.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Generate Schedule
                    </a>
                </div>
            @endif
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
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
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
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-danger { border-left: 4px solid #e74a3b !important; }
</style>
@endsection

@section('scripts')
<script>
function cancelSchedule(scheduleId) {
    const form = document.getElementById('cancelForm');
    form.action = `/payment-schedules/${scheduleId}/cancel`;
    $('#cancelModal').modal('show');
}
</script>
@endsection
