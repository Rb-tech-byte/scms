<div class="row g-4">
    {{-- Main Content Area --}}
    <div class="col-xxl-8">
        {{-- Tabbed Content Card --}}
        <div class="card chart-card mb-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs border-0" id="dashboardNav" role="tablist">
                    <li class="nav-item me-1">
                        <a class="nav-link active text-white border-0" id="overview-pill" href="#overview"
                            data-bs-toggle="tab" role="tab" aria-controls="overview" aria-selected="true">
                            <i data-feather="dollar-sign" style="width: 16px; height: 16px;"></i>
                            Recent Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white border-0" id="activities-pill" href="#activities"
                            data-bs-toggle="tab" role="tab" aria-controls="activities" aria-selected="false">
                            <i data-feather="user-plus" style="width: 16px; height: 16px;"></i>
                            Latest Registrations
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="dashboardNavContent">
                    {{-- Payments Tab --}}
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-pill">
                        @php
                            $payments = isset($branch)
                                ? \App\Models\Payment::latest()
                                    ->whereIn('applicant_id', \App\Models\Applicant::where('branch_id', $branch->id)->get(['id']))
                                    ->take(5)
                                    ->get()
                                : \App\Models\Payment::latest()->take(5)->get();
                        @endphp
                        
                        <div class="table-responsive">
                            <table class="table modern-table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ $payment->date->format('d M Y') ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        {{ strtoupper(substr($payment->applicant->full_name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <a href="{{ route('applicants.show', $payment->applicant->id) }}" class="text-decoration-none fw-500">
                                                        {{ $payment->applicant->full_name ?? '-' }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('payments.show', $payment->id) }}" class="text-success fw-600">
                                                    TZS {{ number_format($payment->amount, 2, '.', ',') }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-soft text-primary">
                                                    {{ $payment->course->title ?? '-' }}
                                                </span>
                                            </td>
                                            <td>{!! getPaymentStatus($payment->payment_status ?? '0') !!}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i data-feather="inbox" class="text-muted mb-2" style="width: 48px; height: 48px;"></i>
                                                <p class="text-muted mb-0">No recent payments</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Registrations Tab --}}
                    <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-pill">
                        @php
                            $applicants = isset($branch)
                                ? \App\Models\Applicant::latest()->where('branch_id', $branch->id)->take(5)->get()
                                : \App\Models\Applicant::latest()->take(5)->get();
                        @endphp
                        
                        <div class="table-responsive">
                            <table class="table modern-table table-hover">
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
                                    @forelse($applicants as $applicant)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        {{ strtoupper(substr($applicant->full_name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <a href="{{ route('applicants.show', $applicant->id) }}" class="text-decoration-none fw-500">
                                                        {{ $applicant->full_name ?? '-' }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                @foreach ($applicant->enrollments as $enrollment)
                                                    <span class="badge bg-blue-soft text-blue">{{ $enrollment->course->title ?? '-' }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($applicant->enrollments as $enrollment)
                                                    <span class="badge bg-warning-soft text-warning">{{ getDurationClass($enrollment->duration) ?? '-' }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge border border-primary text-primary">
                                                    {{ getEducationLevelClass($applicant->education_level) ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @foreach ($applicant->enrollments as $enrollment)
                                                    <span class="badge bg-success-soft text-success">{{ optional($enrollment->venue)->name ?? '-' }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i data-feather="user-x" class="text-muted mb-2" style="width: 48px; height: 48px;"></i>
                                                <p class="text-muted mb-0">No recent registrations</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Expiring Enrollments --}}
        <div class="card chart-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i data-feather="alert-triangle" style="width: 16px; height: 16px;"></i>
                    Enrollments Expiring Soon (Next 3 Days)
                </span>
                @php
                    $enrollments = isset($branch)
                        ? \App\Models\Enrollment::whereDate('end_date', '<=', \Carbon\Carbon::now()->addDays(3))
                            ->whereIn('venue_id', \App\Models\Venue::where('branch_id', $branch->id)->get(['id']))
                            ->get()
                        : \App\Models\Enrollment::whereDate('end_date', '<=', \Carbon\Carbon::now()->addDays(3))->get();
                @endphp
                <span class="badge bg-danger">{{ count($enrollments) }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table modern-table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Duration</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="user-minus" class="text-danger me-2" style="width: 20px; height: 20px;"></i>
                                            <a href="{{ route('applicants.show', $enrollment->applicant->id) }}" class="text-danger fw-500">
                                                {{ $enrollment->applicant->full_name ?? '-' }}
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-blue-soft text-blue">{{ $enrollment->course->title ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning-soft text-warning">{{ getDurationClass($enrollment->duration) ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger text-white">{{ $enrollment->end_date->format('d/m/Y') ?? '-' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i data-feather="check-circle" class="text-success mb-2" style="width: 48px; height: 48px;"></i>
                                        <p class="text-muted mb-0">No enrollments expiring soon</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-xxl-4">
        {{-- Payment vs Balance Chart --}}
        <div class="card chart-card mb-4">
            <div class="card-header">
                <i data-feather="pie-chart" style="width: 16px; height: 16px;"></i>
                Payment vs Balance Ratio
            </div>
            <div class="card-body">
                <div class="chart-pie mb-4">
                    <canvas id="paymentToBalanceChart" width="100%" height="200"></canvas>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-2 border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-circle fa-sm me-2 text-success"></i>
                            <span class="fw-500">Payments Collected</span>
                        </div>
                        <div class="fw-600 text-success" id="paymentPercentage"></div>
                    </div>
                    <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-2 border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-circle fa-sm me-2 text-danger"></i>
                            <span class="fw-500">Pending Balances</span>
                        </div>
                        <div class="fw-600 text-danger" id="balancePercentage"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Course Enrollments Progress --}}
        @php
            $courses = \App\Models\Course::all();
        @endphp
        <div class="card chart-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i data-feather="trending-up" style="width: 16px; height: 16px;"></i>
                    Course Enrollments
                </span>
                <a class="btn btn-sm btn-light" href="{{ route('courses.create') }}">
                    <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                    New Course
                </a>
            </div>
            <div class="card-body">
                @foreach ($courses->take(6) as $course)
                    @php
                        if (isset($branch)) {
                            $totalEnrollments = $course->enrollments
                                ->whereIn('venue_id', \App\Models\Venue::where('branch_id', $branch->id)->pluck('id'))
                                ->count();
                            $totalEnrollmentsTotal = $courses->sum(function ($course) use ($branch) {
                                return $course->enrollments
                                    ->whereIn('venue_id', \App\Models\Venue::where('branch_id', $branch->id)->pluck('id'))
                                    ->count();
                            });
                        } else {
                            $totalEnrollments = $course->enrollments->count();
                            $totalEnrollmentsTotal = $courses->sum(function ($course) {
                                return $course->enrollments->count();
                            });
                        }

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

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="fw-600 small">{{ $course->title }}</div>
                            <div class="small text-muted">
                                <span class="fw-600">{{ $totalEnrollments }}</span> students
                                <span class="text-primary">({{ number_format($percentage, 1) }}%)</span>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar {{ $cssClass }}" role="progressbar"
                                style="width: {{ $percentage }}%; border-radius: 10px;" 
                                aria-valuenow="{{ $percentage }}"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Branch Managers --}}
        @php
            $users = isset($branch)
                ? \App\Models\User::whereHas('role_', function ($query) {
                    $query->where('name', ['branch-admin']);
                })
                ->whereIn('id', \App\Models\Tutor::where('branch_id', $branch->id)->pluck('user_id'))
                ->oldest()->take(5)->get()
                : \App\Models\User::whereHas('role_', function ($query) {
                    $query->where('name', ['branch-admin']);
                })
                ->oldest()->take(5)->get();
        @endphp
        <div class="card chart-card mb-4">
            <div class="card-header">
                <i data-feather="users" style="width: 16px; height: 16px;"></i>
                Branch Managers
            </div>
            <div class="card-body">
                @forelse($users as $staff)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="avatar avatar-lg me-3 bg-light rounded-circle overflow-hidden" style="width: 48px; height: 48px;">
                            @if ($staff->image)
                                <img class="w-100 h-100 object-fit-cover"
                                    src="{{ asset('storage/profiles/' . $staff->image) }}"
                                    alt="{{ $staff->name }}" />
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary text-white fw-600">
                                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <a class="text-dark fw-600 text-decoration-none d-block"
                                href="{{ route('view.tutor', sst($staff->id)) }}">
                                {{ $staff->name ?? '-' }}
                            </a>
                            <div class="small text-muted">
                                <span class="badge bg-primary-soft text-primary">Branch Admin</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i data-feather="user-x" class="text-muted mb-2" style="width: 48px; height: 48px;"></i>
                        <p class="text-muted mb-0">No managers found</p>
                    </div>
                @endforelse
                
                @if (count($users))
                    <div class="text-center mt-3">
                        <a href="{{ route('tutors.index') }}" class="btn btn-sm btn-outline-primary">
                            View All Managers
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var totalPayments = <?php echo $total_payments; ?>;
        var totalBalances = <?php echo $total_balances; ?>;

        var total = totalPayments + totalBalances;
        var paymentPercentage = total > 0 ? (totalPayments / total) * 100 : 0;
        var balancePercentage = total > 0 ? (totalBalances / total) * 100 : 0;

        var pieChartCanvas = document.getElementById("paymentToBalanceChart");

        var paymentToBalanceChart = new Chart(pieChartCanvas, {
            type: "doughnut",
            data: {
                labels: ["Payments Collected", "Pending Balances"],
                datasets: [{
                    data: [totalPayments, totalBalances],
                    backgroundColor: ["#28a745", "#dc3545"],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                cutoutPercentage: 70,
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var label = data.labels[tooltipItem.index];
                            var value = data.datasets[0].data[tooltipItem.index];
                            return label + ': TZS ' + value.toLocaleString();
                        }
                    }
                }
            }
        });

        document.getElementById("paymentPercentage").textContent = paymentPercentage.toFixed(1) + "%";
        document.getElementById("balancePercentage").textContent = balancePercentage.toFixed(1) + "%";

        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
