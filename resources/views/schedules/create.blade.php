@extends('layouts.app')
@section('title', 'Generate Payment Schedule')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-calendar-plus text-primary"></i> Generate Payment Schedule
            </h1>
            <p class="text-muted mb-0">Create installment payment plans for invoices</p>
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        {{-- Generate Schedule Form --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog"></i> Schedule Configuration
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('payment-schedules.generate') }}" id="scheduleForm">
                        @csrf

                        {{-- Invoice Selection --}}
                        <div class="form-group">
                            <label for="invoice_id">Select Invoice <span class="text-danger">*</span></label>
                            <select name="invoice_id" id="invoice_id" class="form-control select2" required>
                                <option value="">-- Select an Invoice --</option>
                                @foreach($invoices as $inv)
                                    <option value="{{ $inv->id }}" 
                                            data-balance="{{ $inv->balance }}"
                                            data-applicant="{{ $inv->applicant->full_name ?? 'N/A' }}"
                                            {{ (old('invoice_id') ?? request('invoice_id')) == $inv->id ? 'selected' : '' }}>
                                        Invoice #{{ $inv->id }} - {{ $inv->applicant->full_name ?? 'N/A' }} 
                                        (Balance: TZS {{ number_format($inv->balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Only invoices with outstanding balances are shown
                            </small>
                        </div>

                        <div class="row">
                            {{-- Payment Template --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template">Payment Frequency <span class="text-danger">*</span></label>
                                    <select name="template" id="template" class="form-control" required>
                                        <option value="weekly" {{ old('template') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="biweekly" {{ old('template') == 'biweekly' ? 'selected' : '' }}>Bi-Weekly (Every 2 weeks)</option>
                                        <option value="monthly" {{ old('template', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="quarterly" {{ old('template') == 'quarterly' ? 'selected' : '' }}>Quarterly (Every 3 months)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Number of Installments --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="installments">Number of Installments <span class="text-danger">*</span></label>
                                    <input type="number" name="installments" id="installments" 
                                           class="form-control" value="{{ old('installments', 4) }}" 
                                           min="1" max="36" required>
                                    <small class="form-text text-muted">Between 1 and 36 installments</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Start Date --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">First Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" 
                                           class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            {{-- Custom Amount (Optional) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="custom_amount">Custom Amount (Optional)</label>
                                    <input type="number" name="custom_amount" id="custom_amount" 
                                           class="form-control" value="{{ old('custom_amount') }}" 
                                           step="0.01" min="0" placeholder="Leave blank to use invoice balance">
                                    <small class="form-text text-muted">Override invoice balance if needed</small>
                                </div>
                            </div>
                        </div>

                        {{-- Preview Section --}}
                        <div id="previewSection" class="mt-4" style="display: none;">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-eye"></i> Schedule Preview
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Due Date</th>
                                            <th>Amount (TZS)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="previewTableBody">
                                        <!-- Preview rows will be inserted here -->
                                    </tbody>
                                    <tfoot class="font-weight-bold">
                                        <tr>
                                            <td colspan="2" class="text-right">Total:</td>
                                            <td id="previewTotal">0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="form-group mt-4">
                            <button type="button" id="previewBtn" class="btn btn-info">
                                <i class="fas fa-eye"></i> Preview Schedule
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Generate Schedule
                            </button>
                            <a href="{{ route('payment-schedules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Invoice Details Sidebar --}}
        <div class="col-lg-4">
            @if($invoice)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-file-invoice"></i> Invoice Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Invoice ID:</dt>
                            <dd class="col-sm-7">#{{ $invoice->id }}</dd>

                            <dt class="col-sm-5">Applicant:</dt>
                            <dd class="col-sm-7">{{ $invoice->applicant->full_name ?? 'N/A' }}</dd>

                            <dt class="col-sm-5">Balance:</dt>
                            <dd class="col-sm-7 text-danger font-weight-bold">
                                TZS {{ number_format($invoice->balance, 2) }}
                            </dd>

                            <dt class="col-sm-5">Paid Amount:</dt>
                            <dd class="col-sm-7 text-success">
                                TZS {{ number_format($invoice->paid_amount, 2) }}
                            </dd>

                            <dt class="col-sm-5">Status:</dt>
                            <dd class="col-sm-7">
                                <span class="badge badge-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </dd>
                        </dl>

                        @if($invoice->paymentSchedules->count() > 0)
                            <hr>
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> This invoice has {{ $invoice->paymentSchedules->count() }} existing schedule(s). 
                                Generating a new schedule will cancel pending ones.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-info text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-info-circle"></i> Instructions
                        </h6>
                    </div>
                    <div class="card-body">
                        <ol class="mb-0 pl-3">
                            <li class="mb-2">Select an invoice with an outstanding balance</li>
                            <li class="mb-2">Choose the payment frequency (weekly, monthly, etc.)</li>
                            <li class="mb-2">Set the number of installments</li>
                            <li class="mb-2">Select the first payment date</li>
                            <li class="mb-2">Preview the schedule before generating</li>
                            <li class="mb-0">Click "Generate Schedule" to create</li>
                        </ol>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Preview button click
    $('#previewBtn').click(function() {
        generatePreview();
    });

    // Auto-preview on input change
    $('#invoice_id, #template, #installments, #start_date, #custom_amount').change(function() {
        if ($('#invoice_id').val()) {
            generatePreview();
        }
    });
});

function generatePreview() {
    const invoiceId = $('#invoice_id').val();
    const template = $('#template').val();
    const installments = parseInt($('#installments').val());
    const startDate = new Date($('#start_date').val());
    const customAmount = parseFloat($('#custom_amount').val()) || null;

    if (!invoiceId || !template || !installments || !startDate) {
        return;
    }

    // Get invoice balance
    const selectedOption = $('#invoice_id option:selected');
    const balance = customAmount || parseFloat(selectedOption.data('balance'));

    if (balance <= 0) {
        alert('Invoice has no outstanding balance');
        return;
    }

    // Calculate installment amount
    const perAmount = Math.round((balance / installments) * 100) / 100;
    let totalAllocated = 0;

    // Generate preview rows
    const tbody = $('#previewTableBody');
    tbody.empty();

    for (let i = 0; i < installments; i++) {
        const dueDate = calculateDueDate(startDate, template, i);
        let amount = perAmount;

        // Adjust last installment for rounding
        if (i === installments - 1) {
            amount = Math.round((balance - totalAllocated) * 100) / 100;
        }

        totalAllocated += amount;

        const row = `
            <tr>
                <td>${i + 1}</td>
                <td>${formatDate(dueDate)}</td>
                <td>${amount.toFixed(2)}</td>
            </tr>
        `;
        tbody.append(row);
    }

    $('#previewTotal').text(balance.toFixed(2));
    $('#previewSection').slideDown();
}

function calculateDueDate(startDate, template, index) {
    const date = new Date(startDate);
    
    switch(template) {
        case 'weekly':
            date.setDate(date.getDate() + (index * 7));
            break;
        case 'biweekly':
            date.setDate(date.getDate() + (index * 14));
            break;
        case 'monthly':
            date.setMonth(date.getMonth() + index);
            break;
        case 'quarterly':
            date.setMonth(date.getMonth() + (index * 3));
            break;
    }
    
    return date;
}

function formatDate(date) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${date.getDate()} ${months[date.getMonth()]}, ${date.getFullYear()}`;
}
</script>
@endsection
