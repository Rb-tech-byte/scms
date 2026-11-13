@extends('layouts.app')
@section('title', 'Payment Receipt ' . $payment->receipt_number)
@section('content')
<div class="container-xl px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
        <h1 class="h3 mb-0">Official Payment Receipt</h1>
        <div>
            <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fas fa-print me-1"></i> Print Receipt</button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                <div>
                    <h4 class="text-primary mb-1">THE OPEN UNIVERSITY OF TANZANIA</h4>
                    <div class="small text-muted">Institute of Educational and Management Technologies (SCMIS)</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold">Receipt #: <code>{{ $payment->receipt_number }}</code></div>
                    <div class="small text-muted">Date: {{ \Carbon\Carbon::parse($payment->date)->format('d-m-Y H:i') }}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div><strong>Student:</strong> {{ $payment->applicant?->full_name ?? '-' }}</div>
                    <div><strong>Course:</strong> {{ $payment->course?->title ?? '-' }}</div>
                    <div><strong>Centre:</strong> {{ $payment->invoice?->enrollment?->venue?->branch?->name ?? '-' }}</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div><strong>Invoice #:</strong> {{ $payment->invoice?->invoice_prefix ?? '-' }}</div>
                    <div><strong>Control #:</strong> {{ $payment->invoice?->control_number ?? '-' }}</div>
                    <div><strong>Method:</strong> {{ ucwords(str_replace('_', ' ', $payment->payment_method ?? 'cash')) }}</div>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Transaction Reference</th>
                        <th>Installment</th>
                        <th>Status</th>
                        <th class="text-end">Amount Paid (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payment->transaction_reference ?? $payment->receipt_number }}</td>
                        <td>{{ ucfirst($payment->installment_type ?? 'full') }}</td>
                        <td><span class="badge bg-{{ $payment->verification_status === 'verified' ? 'success' : 'warning' }}">{{ ucfirst($payment->verification_status ?? 'verified') }}</span></td>
                        <td class="text-end fw-bold">{{ number_format((float)$payment->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-3">
                <strong>Remaining Invoice Balance:</strong> {{ number_format((float)($payment->invoice?->balance ?? 0), 2) }} TZS
            </div>
        </div>
    </div>
</div>
@endsection

