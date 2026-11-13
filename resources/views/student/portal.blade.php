@extends('layouts.app')
@section('title', 'Student Course & Billing Portal')
@section('content')
<div class="container-xl px-4 mt-4">
    <h1 class="h3 mb-3">Student Portal: Course Enrollment, Invoices, Control Numbers & Receipts</h1>

    @if($overdueSchedules->count() > 0)
        <div class="alert alert-danger">
            <strong>Payment Overdue Alert:</strong>
            You have {{ $overdueSchedules->count() }} overdue installment(s):
            <ul class="mb-0 mt-1">
                @foreach($overdueSchedules as $sched)
                    <li>
                        {{ $sched->invoice?->enrollment?->course?->title ?? 'Course' }} —
                        {{ number_format((float)$sched->amount, 2) }} TZS due {{ $sched->due_date?->format('d-m-Y') }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header fw-bold text-primary">Enroll in a New Short Course</div>
        <div class="card-body">
            <form method="POST" action="{{ route('student.enroll') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Course *</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">-- Select Course --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">
                                {{ $course->title }} ({{ $course->teachingMethodLabel() }} - {{ number_format((float)$course->fee) }} TZS / {{ $course->duration }} wk)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Centre / Venue *</label>
                    <select name="venue_id" class="form-select" required>
                        <option value="">-- Select Venue --</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}">{{ $venue->branch?->name ?? 'Centre' }} — {{ $venue->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Session *</label>
                    <select name="session" class="form-select" required>
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Enroll</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-bold text-primary">My Enrolled Courses, Invoices & Receipts</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Method</th>
                        <th>Centre / Venue</th>
                        <th>Schedule Dates</th>
                        <th>Invoice / Control #</th>
                        <th>Balance (TZS)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                        @php $invoice = $enrollment->invoices; @endphp
                        <tr>
                            <td>
                                <strong>{{ $enrollment->course?->title ?? '-' }}</strong><br>
                                <small class="text-muted">{{ ucfirst($enrollment->session) }} session</small>
                            </td>
                            <td>{{ $enrollment->course?->teachingMethodLabel() ?? 'Face to Face' }}</td>
                            <td>{{ $enrollment->venue?->branch?->name ?? '-' }} / {{ $enrollment->venue?->name ?? '-' }}</td>
                            <td>
                                {{ $enrollment->start_date?->format('d-m-Y') }} to {{ $enrollment->end_date?->format('d-m-Y') }}
                            </td>
                            <td>
                                @if($invoice)
                                    <div>Invoice: <code>{{ $invoice->invoice_prefix }}</code></div>
                                    <div>Control #: <code>{{ $invoice->control_number ?? 'Not generated' }}</code></div>
                                    <a href="{{ url('/ViewInvoice/' . $invoice->id) }}" target="_blank" class="small">View Invoice PDF</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($invoice)
                                    <strong>{{ number_format((float)$invoice->balance, 2) }}</strong>
                                    <div>
                                        <span class="badge bg-{{ $invoice->status === '1' ? 'success' : ($invoice->status === '2' ? 'warning' : 'danger') }}">
                                            {{ $invoice->status === '1' ? 'Paid' : ($invoice->status === '2' ? 'Partial' : 'Unpaid') }}
                                        </span>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @if($invoice && empty($invoice->control_number))
                                        <form method="POST" action="{{ route('invoices.control-number', $invoice) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Control #</button>
                                        </form>
                                    @endif

                                    @if($invoice && (float)$invoice->balance > 0)
                                        <a href="{{ route('student.invoices.pay', $invoice) }}" class="btn btn-sm btn-success">Pay / Upload Receipt</a>
                                    @endif

                                    @if($invoice && (float)$invoice->paid_amount <= 0)
                                        <form method="POST" action="{{ route('student.unenroll', $enrollment) }}" onsubmit="return confirm('Unenroll from this course?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Unenroll</button>
                                        </form>
                                    @endif

                                    @if($invoice)
                                        @foreach($invoice->payments as $pmt)
                                            <a href="{{ route('payments.receipt', $pmt) }}" class="btn btn-sm btn-outline-secondary">
                                                Receipt {{ $pmt->receipt_number }}
                                            </a>
                                        @endforeach
                                    @endif

                                    @if($enrollment->certificate && in_array($enrollment->certificate->status, ['ready', 'issued'], true))
                                        <a href="{{ route('certificates.pdf', $enrollment->certificate) }}" class="btn btn-sm btn-primary">
                                            Download Certificate
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No active course enrollments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

