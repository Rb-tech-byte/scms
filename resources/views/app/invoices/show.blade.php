@extends('layouts.app')
@section('title', 'Show invoice')
@section('header')
    {!! $generatePageHeader(
        'Show invoice',
        'invoices.index',
        'file-text',
        'Manage invoicess',
        'file-text',
        'invoices.create',
        'plus',
        'Show invoice',
    ) !!}
@endsection
@section('content')

    <div class="row">
        <div class="col-sm-12 m-auto col-lg-10">
            <div class="card">
                <div class="card-body">

                    <!-- Invoice Logo-->
                    <div class="clearfix">
                        <div class="float-start mb-3">
                            <img src="{{ asset('mini.png') }}" alt="" height="120">
                        </div>
                        <div class="float-end text-end">
                            <div class="invoice-status d-flex">
                                <div class="invoice-prefix">
                                    <h4 class="m-0 d-print-none mx-2 text-white bg-primary p-1">
                                        #{{ $invoice->invoice_prefix ?? '-' }} </h4>
                                </div>
                                <div class="invoice-status">
                                    @php
                                        $status = $invoice->status;
                                        $status_color = 'bg-primary';
                                        $text_color = 'text-primary';
                                        if ($status == '0') {
                                            $status_color = 'bg-danger';
                                            $text_color = 'text-danger';
                                            $message = 'Unpaid';
                                        } elseif ($status == '1') {
                                            $status_color = 'bg-success';
                                            $text_color = 'text-success';
                                            $message = 'Paid';
                                        } elseif ($status == '2') {
                                            $status_color = 'bg-warning';
                                            $text_color = 'text-warning';
                                            $message = 'Partially Paid';
                                        }
                                    @endphp
                                    <h4 class="m-0 d-print-none mx-2 text-white {{ $status_color }} p-1">
                                        {{ $message ?? '-' }}</h4>
                                </div>

                            </div>

                            {{-- billing date  --}}
                            <p class="font-13 mt-3"><span style="text-align: right;">Billing Date: &nbsp; </span> <span
                                    class="float-end">{{ $invoice->date->format('d-m-Y') ?? '-' }}</span></p>

                        </div>
                    </div>
                    <!-- Invoice Detail-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="text">
                                {{ $site_settings->invoice_site_title ?? 'OPEN UNIVERSITY OF TANZANIA' }}<br>
                                {{ $site_settings['site_head_quarter'] ?? 'Head Quarters Kinondoni' }}<br>
                                {{ $site_settings['site_po_box'] ?? 'P.O.BOX 23409' }}, <br>
                                {{ $site_settings['site_po_box_location'] ?? 'Dar es Salaam, Tanzania' }}<br>
                                <span class="telephone">{{ $site_settings['site_contact_number'] ?? '-' }}</span><br>
                                <span class="email">{{ $site_settings['site_contact_email'] ?? '-' }}</span><br>
                            </div>
                        </div><!-- end col -->
                        <div class="col-sm-4 offset-sm-2 mt-n5">
                            <div class="mt-3 float-sm-end">
                                <table>
                                    <tr>
                                        <td class="text-end text-bold bold">Invoice To :&nbsp;</td>
                                        <td class="value text-start">{{ optional($invoice->applicant)->full_name ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-muted">Enrollment Date :&nbsp;</td>
                                        <td class="value text-start">{{ $invoice->date->format('d-m-Y') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-muted">Invoice Number :&nbsp;</td>
                                        <td class="value text-start">{{ $invoice->invoice_prefix ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-muted">Receipt Number(s) :&nbsp;</td>
                                        <td class="value text-start">
                                            @php
                                                $receiptNumbers = $invoice->payments->pluck('receipt_number')->filter()->implode(', ');
                                            @endphp
                                            {{ $receiptNumbers ?: '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-muted">Installment :&nbsp;</td>
                                        <td class="value text-start">
                                            @php
                                                $latest = $invoice->payments->sortByDesc('date')->first();
                                                $installmentLabel = '-';
                                                if($latest){
                                                    $map = ['quarter' => 'Quarter (25%)', 'half' => 'Half (50%)', 'full' => 'Full (100%)'];
                                                    $installmentLabel = $map[$latest->installment_type] ?? ($latest->payment_status ? 'Partial' : 'Full');
                                                }
                                            @endphp
                                            {{ $installmentLabel }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-muted">Invoice Status :&nbsp;</td>
                                        <td class="value text-start"> <span
                                                class="{{ $text_color }}">{{ $message }} </span> </td>

                                    </tr>
                                </table>


                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                    <!-- end row -->

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table mt-4">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Course</th>
                                            <th>Quantity</th>
                                            <th class="text-end">Enrollment Cost (Tsh)</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td>1. </td>
                                            <td>{{ $invoice->enrollment->course->title ?? '-' }}</td>
                                            <td>1</td>
                                            <td class="text-end">
                                                {{ number_format($invoice->enrollment->course->fee, 2, '.', ',') }}</td>
                                            <td class="text-end">
                                                {{ number_format($invoice->enrollment->course->fee, 2, '.', ',') }} Tsh
                                            </td>

                                        </tr>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-6">
                            <div class="clearfix pt-3">
                                <h6 class="text-muted">Notes:</h6>
                                <small>
                                    {!! $site_settings['invoice_footer'] ?? '--' !!}
                                </small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-end mt-3 mt-sm-0">
                                <p><b>Sub-total:</b> <span
                                        class="float-end">{{ number_format($invoice->enrollment->course->fee, 2, '.', ',') }}
                                        Tsh</span></p>
                                <p><b class="text-red">Discount:</b> <span
                                        class="float-end text-red">{{ number_format($invoice->discount_amount, 2, '.', ',') }}
                                        Tsh</span></p>

                                @php
                                    $latest = $invoice->payments->sortByDesc('date')->first();
                                    $isPartialInstallment = in_array(optional($latest)->installment_type, ['quarter','half']);
                                    $prefix = ($invoice->status == '2' || $isPartialInstallment) ? '- ' : '';
                                @endphp
                                <p><b class="h3">Balance: &nbsp; </b> <span
                                        class="float-end h3">{{ $prefix }}{{ number_format($invoice->balance, 2, '.', ',') }} Tsh</span>
                                </p>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>


                    <div class="d-print-none mt-4">
                        <div class="text-end">
                            <a href="{{ route('invoice_pdf.generate', $invoice) }}" target="_blank" class="btn btn-primary">Print &nbsp; <i class="fa fa-print"></i></a>
                            <!--<a href="javascript:window.print()" class="btn btn-primary"><i class="mdi mdi-printer"></i>-->
                            <!--    Print</a>-->
                        </div>
                    </div>
                    <!-- end buttons -->

                </div> <!-- end card-body-->
            </div> <!-- end card -->
        </div> <!-- end col-->
    </div>

    {{--    a row that shows all payments made for this invoice --}}
    <div class="row my-4 d-print-none">


        <div class="col-sm-12 m-auto col-lg-10">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h3 class="text-center">Payments Associated with invoice</h3>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                @if(auth()->user()->hasRole('system-admin'))
                                    <th>Received By</th>
                                    @check('Delete payments')
                                    <th>Actions</th>
                                @endcheck
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->payments as $payment)
                                <tr>
                                    <td>{{ $payment->date->format('d/m/Y') }}</td>
                                    <td>{{ number_format($payment->amount, '2', '.', ',') }}</td>
                                    @check('Delete payments')
                                        <td>
                                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    @endcheck
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>

@endsection
