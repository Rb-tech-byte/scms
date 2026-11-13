@extends('layouts.app')
@section('title', 'Submit Payment & Upload Receipt')
@section('content')
<div class="container-xl px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Submit Payment & Upload Receipt</h1>
        <a href="{{ route('student.portal') }}" class="btn btn-outline-secondary btn-sm">Back to Student Portal</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Course:</strong> {{ $invoice->enrollment?->course?->title ?? '-' }}</div>
                <div class="col-md-4"><strong>Invoice #:</strong> <code>{{ $invoice->invoice_prefix }}</code></div>
                <div class="col-md-4"><strong>Control #:</strong> <code>{{ $controlNumber }}</code></div>
            </div>
            <div class="alert alert-info">
                Outstanding Balance: <strong>{{ number_format((float)$invoice->balance, 2) }} TZS</strong>
            </div>

            <form method="POST" action="{{ route('student.invoices.submit-payment', $invoice) }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Amount Paid (TZS) *</label>
                        <input type="number" step="0.01" min="1" max="{{ $invoice->balance }}" name="amount" class="form-control" value="{{ old('amount', $invoice->balance) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Method *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="control_number">Control Number (GePG)</option>
                            <option value="mobile_money">Mobile Money (M-Pesa / TigoPesa / Airtel)</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash Deposit</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Transaction / Receipt Reference *</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. TXN8839201" value="{{ old('transaction_reference') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Upload Payment Receipt (PDF, JPG, PNG) *</label>
                        <input type="file" name="receipt_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Submit Payment & Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

