@extends('layouts.app')
@section('title', 'Discount & Waiver Audit')
@section('content')
<div class="container-xl px-4 mt-4">
    <h1 class="h3 mb-3">Discount & Fee Waiver Audit</h1>
    <div class="alert alert-info">
        Total Fee Waivers Granted: <strong>{{ number_format((float)$totalWaived, 2) }} TZS</strong> across {{ $discountedInvoices->count() }} invoice(s).
    </div>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Student</th>
                        <th>Centre</th>
                        <th>Course</th>
                        <th>Reason</th>
                        <th class="text-end">Discount (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discountedInvoices as $inv)
                        <tr>
                            <td><code>{{ $inv->invoice_prefix }}</code></td>
                            <td>{{ $inv->applicant?->full_name ?? '-' }}</td>
                            <td>{{ $inv->applicant?->branch?->name ?? '-' }}</td>
                            <td>{{ $inv->enrollment?->course?->title ?? '-' }}</td>
                            <td>{{ $inv->discount_reason ?? 'Institutional Waiver' }}</td>
                            <td class="text-end">{{ number_format((float)$inv->discount_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No discounted invoices recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
