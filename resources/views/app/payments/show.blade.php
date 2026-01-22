@extends('layouts.app')
@section('title', 'View payment')
@section('header')
    {!! $generatePageHeader(
        'View payment',
        'payments.index',
        'dollar-sign',
        'Manage paymentss',
        'dollar-sign',
        'payments.create',
        'plus',
        'New payment',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Payment Details</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <h5 class="text-muted">@lang('crud.payments.inputs.applicant_id')</h5>
                <p class="mb-0">{{ optional($payment->applicant)->full_name ?? '-' }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <h5 class="text-muted">@lang('crud.payments.inputs.course_id')</h5>
                <p class="mb-0">{{ optional($payment->course)->title ?? '-' }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <h5 class="text-muted">@lang('crud.payments.inputs.payment_status')</h5>
                <p class="mb-0">{!! getPaymentStatus($payment->payment_status ?? '0') !!}</p>
            </div>
            <div class="col-md-6 mb-3">
                <h5 class="text-muted">@lang('crud.payments.inputs.amount')</h5>
                <p class="mb-0 text-success">{{number_format($payment->amount, 2, '.', ',').' Tsh' ?? '-'}}</p>
            </div>
            <div class="col-md-6 mb-3">
                <h5 class="text-muted">Remaining Balance</h5>
                <p class="mb-0 text-danger">{{number_format($payment->invoice->balance, 2, '.', ',').' Tsh' ?? '-'}}</p>
                {{-- invoice --}}
                <p class="ml-n3">
                    <a href="{{ route('invoices.show', $payment->invoice) }}" class="btn  btn-link">
                        <i class="icon ion-md-eye"></i>
                        View Invoice | {{$payment->invoice->invoice_prefix}}
                    </a>
                </p>

            </div>
            <div class="col-md-6 mb-3">
                <h5 class="text-muted">Due Date</h5>
                <p class="mb-0">{{ $payment->date->format('d/m/Y') ?? '-' }}</p>
            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-between">
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                <i class="icon ion-md-return-left"></i> @lang('crud.common.back')
            </a>

            @check('Add payments')
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
            @endcheck
        </div>
    </div>
</div>

    </div>
@endsection
