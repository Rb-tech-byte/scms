@extends('layouts.app')
@section('title', 'Show Student')
@section('header')
    {!! $generatePageHeader('Show Student', 'applicants.index', 'user', 'Student', 'users') !!}
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ asset('mini.png') }}" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">

                    <h4 class="mb-0 mt-2">{{ $applicant->full_name ?? '-' }}</h4>
                    <p class="text-muted font-14">{{ 'Student' }}</p>

                    <div style="display: flex; align-items: center; justify-content:center;">
                        <a href="{{ route('applicants.edit', $applicant) }}"
                            class="btn mx-2 btn-success btn-sm mb-2">Edit</a>

                        @check('Delete applicants')
                                <button type="submit" class="btn btn-danger btn-sm mb-2 deletebtn" link="{{ route('applicants.destroy', $applicant) }}">
                                    <i class="fa-solid fa-trash-alt"></i>
                                </button>
                        @endcheck
                    </div>
                    <ul class="social-list list-inline mt-3 mb-0">
                        <li class="list-inline-item">
                            <a href="javascript: void(0);" class="social-list-item border-primary text-primary"><i
                                    class="mdi mdi-facebook"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="javascript: void(0);" class="social-list-item border-danger text-danger"><i
                                    class="mdi mdi-google"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="javascript: void(0);" class="social-list-item border-info text-info"><i
                                    class="mdi mdi-twitter"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="javascript: void(0);" class="social-list-item border-secondary text-secondary"><i
                                    class="mdi mdi-github"></i></a>
                        </li>
                    </ul>
                </div> <!-- end card-body -->
            </div> <!-- end card -->



        </div> <!-- end col-->

        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                        <li class="nav-item">
                            <a href="#aboutme" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0 active">
                                PROFILE
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#timeline" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                                INVOICES
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#payments" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                                PAYMENTS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#settings" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0">
                                Settings
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="aboutme">

                            <h5 class="text-uppercase">
                                <i class="mdi mdi-briefcase me-1"></i>
                            </h5>

                                <div class="card-body">
                                    <div class="container mt-4">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.full_name')</th>
                                                    <td>{{ $applicant->full_name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.home_address')</th>
                                                    <td>{{ $applicant->home_address ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.country_id')</th>
                                                    <td>{{ optional($applicant->country)->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.office_of_work')</th>
                                                    <td>{{ $applicant->office_of_work ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.occupation')</th>
                                                    <td>{{ $applicant->occupation ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.mobile')</th>
                                                    <td>{{ $applicant->mobile ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.email')</th>
                                                    <td>{{ strtolower($applicant->email) ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.phone_2')</th>
                                                    <td>{{ $applicant->phone_2 ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.name_of_sponsor')</th>
                                                    <td>{{ $applicant->name_of_sponsor ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('crud.applicants.inputs.education_level')</th>
                                                    <td>{{ getEducationLevelClass($applicant->education_level) ?? '-' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                        </div> <!-- end tab-pane -->
                        <!-- end about me section content -->

                        <div class="tab-pane show" id="timeline">
                            {{-- Fetch all invoices of this applicant and display them here in table  --}}

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Invoice Number</th>
                                                <th>Balance</th>
                                                <th>Paid Amount (Tsh)</th>
                                                <th>Discount Amount (Tsh)</th>
                                                <th>Course Enrolled</th>
                                                <th>Payment Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($applicant->invoices as $invoice)
                                                <tr>
                                                    <td><a href="{{ route('invoices.show', $invoice) }}"
                                                        class="text-primary">{{ $invoice->invoice_prefix }}</a></td>
                                                    <td>{{ number_format($invoice->balance, 2, '.', ',') ?? 0 . ' Tzs' }}
                                                    </td>
                                                    <td>{{ number_format($invoice->paid_amount, 2, '.', ',') }}</td>
                                                    <td class="text-danger">{{ number_format($invoice->discount_amount, 2, '.', ',') }}</td>
                                                    <td>{{ $invoice->enrollment->course->title ?? '-' }}</td>
                                                    <td>{!! getStatusBadge($invoice->status ?? '0') !!}</td>
                                                    <td>
                                                        <a href="{{ route('invoices.show', $invoice) }}"
                                                            class="btn btn-sm btn-primary">View</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">No invoices found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </div>
                        <div class="tab-content">
                            <div class="tab-pane" id="payments">

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Payment For</th>
                                                    <th>Paid Amount (Tsh)</th>
                                                    <th>Invoice</th>
                                                    <th>Attachment (receipt)</th>
                                                    <th>Payment Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($applicant->payments as $payment)
                                                    {{--                                                    {{dd($payment->invoice->enrollment->course->title)}} --}}
                                                    <tr>
                                                        <td>{{ $payment->date->format('d-m-Y') }}</td>
                                                        <td>{{ $payment->invoice->enrollment->course->title ?? '-' }}</td>
                                                        <td>{{ $payment->invoice->paid_amount ?? 0 . ' Tzs' }}</td>
                                                        <td><a href="{{ route('invoices.show', $payment->invoice->id) }}"
                                                                target="_blank"
                                                                onclick="window.open('{{ route('invoices.show', $payment->invoice->id) }}', 'popup', 'width=800,height=600'); return false;">view</a>
                                                        </td>
                                                        <td>
                                                            @if ($payment->receipt)
                                                                <a href="{{ url(\Storage::url('receipts/' . $payment->receipt)) }}"
                                                                    target="_blank"
                                                                    onclick="window.open('{{ url(\Storage::url('receipts/' . $payment->receipt)) }}', 'popup', 'width=800,height=600'); return false;"><span
                                                                        class="bg-primary-soft text-primary">view
                                                                        receipt</span></a>
                                                            @else
                                                                <span class="bg-danger-soft text-danger">No receipt</span>
                                                            @endif
                                                        </td>
                                                        <td>{!! getPaymentStatus($payment->payment_status ?? '0') !!}</td>
                                                        <td>
                                                            <a href="{{ route('payments.show', $payment) }}"
                                                                class="btn btn-sm btn-info">View</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6">No payments found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- end timeline content-->

                        <div class="tab-pane" id="settings">

                        </div>
                        <!-- end settings content-->

                    </div> <!-- end tab-content -->
                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>
@endsection
