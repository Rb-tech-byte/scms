@extends('layouts.app')
@section('title', 'invoice')
@section('header')

@php
if(auth()->user()->hasRole(['branch-admin','system-admin'])){
    $pageHeaderTitle = 'Manage Invoices';
}else{
    $pageHeaderTitle = 'My Invoices';
}

@endphp

    {!! $generatePageHeader(
        'invoices',
        'invoices.index',
        'file-text',
        $pageHeaderTitle,
        'file-text',
        '',
        '',
        '',
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
                                @lang('crud.invoices.inputs.applicant_id')
                            </th>
                            <th class="text-left">
                                Course Enrolled
                            </th>

                            <th class="text-right">
                                Balance (TZS)
                            </th>
                            <th class="text-right">
                                Paid amount (TZS)
                            </th>

                            <th class="text-left">
                                @lang('crud.invoices.inputs.status')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    {{ optional($invoice->applicant)->full_name ?? '-' }}
                                </td>
                                <td>
                                    {{ optional($invoice->enrollment)->course->title ?? '-' }}
                                </td>

                                @php
                                    $latest = $invoice->payments->sortByDesc('date')->first();
                                    $isPartialInstallment = in_array(optional($latest)->installment_type, ['quarter','half']);
                                    $prefix = ($invoice->status == 2 || $isPartialInstallment) ? '- ' : '';
                                @endphp
                                <td>{{ $prefix }}{{ number_format($invoice->balance, 2, '.', ',') ?? '-' }}</td>
                                <td>{{ number_format($invoice->paid_amount, 2, '.', ',') ?? '-' }}</td>

                                <td>
                                    @if ($invoice->status == 1)
                                        <div class="badge bg-success rounded-pill">Paid</div>
                                    @elseif($invoice->status == 2)
                                        <div class="badge bg-warning rounded-pill">partial Paid</div>
                                    @else
                                        <div class="badge bg-danger rounded-pill">Not Paid</div>
                                    @endif
                                </td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        @check('Update invoices')
                                            <a href="{{ route('invoices.edit', $invoice) }}">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('View invoices')
                                            <a href="{{ route('invoices.show', $invoice) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('Delete invoices')
                                                    <button type="submit" class="btn btn-outline-danger deletebtn" link="{{ route('invoices.destroy', $invoice) }}">
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
