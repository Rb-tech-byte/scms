<div class="row">
    <div class="col-xxl-8">
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <ul class="nav nav-tabs card-header-tabs" id="dashboardNav" role="tablist">
                    <li class="nav-item me-1"><a class="nav-link active" id="overview-pill" href="#overview"
                            data-bs-toggle="tab" role="tab" aria-controls="overview" aria-selected="true">Fee
                            collections</a></li>
                    <li class="nav-item"><a class="nav-link" id="activities-pill" href="#activities"
                            data-bs-toggle="tab" role="tab" aria-controls="activities" aria-selected="false">Latest
                            Registration</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="dashboardNavContent">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel"
                        aria-labelledby="overview-pill">
                        @php
                            $payments = \App\Models\Payment::latest()
                                ->take(5)
                                ->get();
                        @endphp
                        <table class="datatable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Amount (Tsh)</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->date->format('d/m/Y') ?? '-' }}</td>
                                        <td>
                                            <i class="me-2 text-green" data-feather="zap"></i>
                                            <a
                                                href="{{ route('applicants.show', $payment->applicant->id) }}">{{ $payment->applicant->full_name ?? '-' }}</a>

                                        </td>
                                        <td><a
                                                href="{{ route('payments.show', $payment->id) }}">{{ number_format($payment->amount, 2, '.', ',') ?? '-' }}</a>
                                        </td>
                                        <td>{{ $payment->course->title ?? '-' }}</td>
                                        <td>{!! getPaymentStatus($payment->payment_status ?? '0') !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">No payments</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-pill">
                        <table class="datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Duration</th>
                                    <th>Education</th>
                                    <th>Venue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $applicants = \App\Models\Applicant::latest()
                                        ->take(5)
                                        ->get();
                                @endphp
                                @forelse($applicants as $applicant)
                                    <tr>
                                        <td>
                                            <i class="me-2 text-green" data-feather="user-plus"></i>
                                            <a
                                                href="{{ route('applicants.show', $applicant->id) }}">{{ $applicant->full_name ?? '-' }}</a>

                                        </td>
                                        <td>
                                            @foreach ($applicant->enrollments as $enrollment)
                                                <span
                                                    class="badge bg-blue-soft text-blue">{{ $enrollment->course->title ?? '-' }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($applicant->enrollments as $enrollment)
                                                <span
                                                    class="badge bg-warning-soft text-warning">{{ getDurationClass($enrollment->duration) ?? '-' }}</span>
                                            @endforeach
                                        </td>
                                        <td><span
                                                class="badge font-large bg-blue-soft text-blue">{{ getEducationLevelClass($applicant->education_level) ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @foreach ($applicant->enrollments as $enrollment)
                                                <span
                                                    class="badge bg-success-soft text-success">{{ optional($enrollment->venue)->name ?? '-' }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">No applicants</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 mb-4">
                <div class="card card-header-actions h-100">
                    <div class="card-header">
                        <span class="text-primary">Enrollments Expires in 3 days to came</span>
                        @php
                            $enrollments = \App\Models\Enrollment::whereDate('end_date', '<=', \Carbon\Carbon::now()->addDays(3))->get();
                        @endphp
                    </div>
                    <div class="card-body">
                        <table class="datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Duration</th>
                                    <th>End date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            <i class="me-2 text-danger" data-feather="user-minus"></i>
                                            <a href="{{ route('applicants.show', $enrollment->applicant->id) }}"
                                                class="text-danger">{{ $enrollment->applicant->full_name ?? '-' }}</a>

                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-blue-soft text-blue">{{ $enrollment->course->title ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-warning-soft text-warning">{{ getDurationClass($enrollment->duration) ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-danger text-light">{{ $enrollment->end_date->format('d/m/Y') ?? '-' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">No enrollments</td>
                                    </tr>
                                @endforelse


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">Ratio of Payments to Balances</div>
                    <div class="card-body">
                        <div class="chart-pie mb-4"><canvas id="paymentToBalanceChart" width="100%"
                                height="50"></canvas>
                        </div>
                        <div class="list-group list-group-flush">
                            <div
                                class="list-group-item d-flex align-items-center justify-content-between small px-0 py-2">
                                <div class="me-3">
                                    <i class="fas fa-circle fa-sm me-1 text-success"></i>
                                    Payments
                                </div>
                                <div class="fw-500 text-dark" id="paymentPercentage"></div>
                            </div>
                            <div
                                class="list-group-item d-flex align-items-center justify-content-between small px-0 py-2">
                                <div class="me-3">
                                    <i class="fas fa-circle fa-sm me-1 text-danger"></i>
                                    Balances
                                </div>
                                <div class="fw-500 text-dark" id="balancePercentage"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4">
        <div class="row">
            <div class="col-xl-6 col-xxl-12">
                <div class="card mb-4">
                    <div class="card-header">Branch Managers</div>
                    <div class="card-body">
                        @php
                            $users = \App\Models\User::whereHas('role', function ($query) {
                                $query->whereIn('name', ['system-admin', 'tutor', 'branch-admin', 'staff']);
                            })->get();
                        @endphp
                        @forelse($users as $staff)

                            <div class=" mb-4">
                                <div class="d-flex align-items-center flex-shrink-0 me-3">
                                    <div class="avatar avatar-xl me-3 mb-3 bg-gray-200">
                                        @php
                                            $staffAvatar = profileImageUrl($staff->image);
                                        @endphp
                                        @if ($staffAvatar)
                                            <img class="avatar-img img-fluid"
                                                src="{{ $staffAvatar }}"
                                                alt=" {{ $staff->name }} " />
                                    </div>
                                @else
                                    <img class="avatar-img img-fluid" src="{{ asset('mini.png') }}"
                                        alt="{{ $staff->name }}" />
                                </div>
                        @endif
                        <div class="d-flex flex-column fw-bold">
                            <a class="text-dark line-height-normal mb-1"
                                href="{{ route('users.show', $staff) }}">{{ $staff->name ?? '-' }}</a>
                            <div class="small text-muted line-height-normal">
                                @forelse ($staff->roles as $role)
                                    <span class="badge bg-blue-soft text-blue">{{ $role->name }}</span>
                                    <br />
                                @empty -
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <span>Empty</span>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
    <div class="col-xl-6 col-xxl-12">
        @php
            $courses = \App\Models\Course::all();
        @endphp
        <div class="card card-header-actions mb-4">
            <div class="card-header">
                Courses with highest enrollments
                <a class="btn btn-sm btn-primary-soft text-primary" href="{{ route('courses.create') }}">Create
                    New</a>
            </div>
            <div class="card-body">
                @foreach ($courses as $course)
                    @php
                        $totalEnrollments = $course->enrollments->count();
                        $totalEnrollmentsTotal = $courses->sum(function ($course) {
                            return $course->enrollments->count();
                        });
                        $percentage = $totalEnrollmentsTotal > 0 ? ($totalEnrollments / $totalEnrollmentsTotal) * 100 : 0;

                        $cssClass = '';
                        if ($percentage > 70) {
                            $cssClass = 'bg-success';
                        } elseif ($percentage > 50) {
                            $cssClass = 'bg-primary';
                        } elseif ($percentage > 40) {
                            $cssClass = 'bg-warning';
                        } elseif ($percentage > 20) {
                            $cssClass = 'bg-info';
                        } else {
                            $cssClass = 'bg-danger';
                        }
                    @endphp

                    <div class="d-flex align-items-center justify-content-between small mb-1">
                        <div class="fw-bold">{{ $course->title }}</div>
                        <div class="small">{{ number_format($percentage, 2) }}%</div>
                    </div>
                    <div class="progress mb-3">
                        <div class="progress-bar  {{ $cssClass }}" role="progressbar"
                            style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
</div>
@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var totalPayments = <?php echo $total_payments; ?>;
            var totalBalances = <?php echo $total_balances; ?>;

            var paymentToBalanceRatio = totalPayments / (totalPayments + totalBalances);
            var balanceToPaymentRatio = totalBalances / (totalPayments + totalBalances);

            var pieChartCanvas = document.getElementById("paymentToBalanceChart");

            var paymentToBalanceChart = new Chart(pieChartCanvas, {
                type: "pie",
                data: {
                    labels: ["Payments", "Balances"],
                    datasets: [{
                        data: [paymentToBalanceRatio, balanceToPaymentRatio],
                        backgroundColor: ["#28a745", "#f54343"],
                    }, ],
                },
            });

            var paymentLegend = document.getElementById("paymentPercentage");
            var balanceLegend = document.getElementById("balancePercentage");

            paymentLegend.textContent = (paymentToBalanceRatio * 100).toFixed(2) + "%";
            balanceLegend.textContent = (balanceToPaymentRatio * 100).toFixed(2) + "%";
        });
    </script>
@endpush
