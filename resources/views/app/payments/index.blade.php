@extends('layouts.app')
@section('title', 'payment')
@section('header')
    {!! $generatePageHeader(
        'payments',
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

        <div class="card">
            <div class="card-body">

                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th class="text-left">
                                @lang('crud.payments.inputs.applicant_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.payments.inputs.course_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.payments.inputs.payment_status')
                            </th>
                            <th class="text-right">
                                @lang('crud.payments.inputs.amount')
                            </th>
                            <th class="text-left">
                                @lang('crud.payments.inputs.date')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>
                                    {{ optional($payment->applicant)->full_name ?? '-' }}
                                </td>
                                <td>
                                    {{ optional($payment->course)->title ?? '-' }}
                                </td>
                                <td>{!! getPaymentStatus($payment->payment_status ?? '0') !!}</td>
                                <td>{{ $payment->amount ?? '-' }}</td>
                                <td>{{ $payment->date ?? '-' }}</td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        @check('Edit payments')
                                            <a href="{{ route('payments.edit', $payment) }}">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('View payments')
                                            <a href="{{ route('payments.show', $payment) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('Delete payments')
                                            
                                                <button type="submit" class="btn btn-outline-danger deletebtn" link="{{ route('payments.destroy', $payment) }}">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                        @endcheck
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection
