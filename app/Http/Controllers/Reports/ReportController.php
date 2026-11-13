<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Venue;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $report;

    /**
     * Generate comprehensive applicant report with complex joins and aggregations
     */
    public function generateApplicantReport(Request $request)
    {
        $courseId = $request->input('course');
        $branchId = $request->input('branch');
        $venueId = $request->input('venue');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $applicantName = $request->input('applicant');
        $paymentStatus = $request->input('payment_status'); // 0=paid, 1=partial, 2=unpaid
        $educationLevel = $request->input('education_level');
        $reportType = $request->input('report_type', 'detailed'); // detailed, summary, financial
        $groupBy = $request->input('group_by'); // course, branch, venue, date
        $branch = session('branch');

        // Base query with optimized eager loading
        $query = Applicant::query()
            ->with([
                'invoices' => function($q) {
                    $q->with(['payments', 'enrollment.course', 'enrollment.venue.branch']);
                },
                'enrollments.course',
                'enrollments.venue',
                'branch',
                'country',
                'user'
            ]);

        // Apply branch filter from session
        if (!empty($branch)) {
            $query->where('branch_id', $branch->id);
        }

        // Apply filters
        if ($courseId) {
            $query->whereHas('enrollments', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($venueId) {
            $query->whereHas('enrollments', function ($q) use ($venueId) {
                $q->where('venue_id', $venueId);
            });
        }

        if ($dateFrom) {
            $query->whereHas('invoices', function ($q) use ($dateFrom) {
                $q->whereDate('date', '>=', $dateFrom);
            });
        }

        if ($dateTo) {
            $query->whereHas('invoices', function ($q) use ($dateTo) {
                $q->whereDate('date', '<=', $dateTo);
            });
        }

        if ($applicantName) {
            $query->where('full_name', 'like', '%' . $applicantName . '%');
        }

        if ($paymentStatus !== null && $paymentStatus !== '') {
            $query->whereHas('invoices', function ($q) use ($paymentStatus) {
                $q->where('status', $paymentStatus);
            });
        }

        if ($educationLevel) {
            $query->where('education_level', $educationLevel);
        }

        // Get applicants
        $applicants = $query->get();

        // Process data based on report type
        $reportData = collect();

        foreach ($applicants as $applicant) {
            foreach ($applicant->invoices as $invoice) {
                $payments = $invoice->payments;
                $enrollment = $invoice->enrollment;

                // Calculate payment details
                $totalPaid = $payments->sum('amount');
                $receiptNumbers = $payments->pluck('receipt_number')->filter()->implode(', ');
                $paymentDates = $payments->pluck('date')->map(fn($d) => $d->format('d/m/Y'))->implode(', ');
                
                // Get payment status
                $invoiceStatus = $invoice->status;
                $statusText = $invoiceStatus == '1' ? 'Paid' : ($invoiceStatus == '2' ? 'Partially Paid' : 'Unpaid');

                // Get installment info
                $firstPayment = $payments->first();
                $installmentType = optional($firstPayment)->installment_type;
                $installmentLabel = $this->getInstallmentLabel($installmentType);

                // Course and venue details
                $course = optional($enrollment)->course;
                $venue = optional($enrollment)->venue;
                $venueBranch = optional($venue)->branch;

                // Build report row
                $row = [
                    // Applicant Info
                    'applicant_id' => $applicant->id,
                    'full_name' => $applicant->full_name,
                    'email' => $applicant->email,
                    'mobile' => $applicant->mobile,
                    'phone_2' => $applicant->phone_2,
                    'education_level' => getEducationLevelClass($applicant->education_level),
                    'home_address' => $applicant->home_address,
                    'occupation' => $applicant->occupation,
                    'office_of_work' => $applicant->office_of_work,
                    'sponsor' => $applicant->name_of_sponsor,
                    'country' => optional($applicant->country)->name,
                    
                    // Enrollment Info
                    'course_id' => optional($course)->id,
                    'course_name' => optional($course)->title,
                    'course_fee' => optional($course)->fee,
                    'venue_name' => optional($venue)->name,
                    'branch_name' => optional($applicant->branch)->name ?? optional($venueBranch)->name,
                    'session' => optional($enrollment)->session,
                    'duration' => getDurationClass(optional($enrollment)->duration),
                    'start_date' => optional($enrollment)->start_date?->format('d/m/Y'),
                    'end_date' => optional($enrollment)->end_date?->format('d/m/Y'),
                    'enrollment_date' => optional($enrollment)->created_at?->format('d/m/Y'),
                    
                    // Invoice Info
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_prefix,
                    'invoice_date' => $invoice->date->format('d/m/Y'),
                    'invoice_status' => $statusText,
                    
                    // Payment Info
                    'total_fee' => $invoice->balance + $totalPaid,
                    'paid_amount' => $totalPaid,
                    'balance' => $invoice->balance,
                    'payment_percentage' => $invoice->balance + $totalPaid > 0 
                        ? round(($totalPaid / ($invoice->balance + $totalPaid)) * 100, 2) 
                        : 0,
                    'installment_type' => $installmentLabel,
                    'receipt_numbers' => $receiptNumbers,
                    'payment_dates' => $paymentDates,
                    'number_of_payments' => $payments->count(),
                    
                    // Discount Info
                    'has_discount' => (bool) $invoice->has_discount,
                    'discount_amount' => $invoice->discount_amount ?? 0,
                    'discount_reason' => $invoice->discount_reason,
                    
                    // Staff Student Info
                    'is_staff_student' => $applicant->is_staff_student ?? false,
                    'is_staff_family_member' => $applicant->is_staff_family_member ?? false,
                    'is_staff' => $applicant->is_staff ?? false,
                    
                    // Timestamps
                    'registered_at' => $applicant->created_at->format('d/m/Y H:i'),
                ];

                $reportData->push($row);
            }
        }

        // Apply grouping if requested
        if ($groupBy && $reportType === 'summary') {
            return $this->groupReportData($reportData, $groupBy);
        }

        return $reportData;
    }

    /**
     * Group report data by specified field
     */
    private function groupReportData($data, $groupBy)
    {
        $grouped = $data->groupBy($groupBy . '_name');
        
        return $grouped->map(function ($items, $key) use ($groupBy) {
            return [
                $groupBy => $key,
                'total_students' => $items->count(),
                'total_enrollments' => $items->count(),
                'total_fee' => $items->sum('total_fee'),
                'total_paid' => $items->sum('paid_amount'),
                'total_balance' => $items->sum('balance'),
                'collection_rate' => $items->sum('total_fee') > 0 
                    ? round(($items->sum('paid_amount') / $items->sum('total_fee')) * 100, 2) 
                    : 0,
                'paid_count' => $items->where('invoice_status', 'Paid')->count(),
                'partial_count' => $items->where('invoice_status', 'Partially Paid')->count(),
                'unpaid_count' => $items->where('invoice_status', 'Unpaid')->count(),
            ];
        })->values();
    }

    /**
     * Get installment label
     */
    private function getInstallmentLabel($type)
    {
        $labels = [
            'quarter' => 'Quarter (25%)',
            'half' => 'Half (50%)',
            'full' => 'Full (100%)',
        ];
        
        return $labels[$type] ?? 'N/A';
    }

    /**
     * Export to Excel
     */
    public function exportApplicantReportToExcel(Request $request)
    {
        $data = $this->generateApplicantReport($request);
        $reports = $data->toArray();
        $fileName = 'applicant_report_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new ReportsExport($reports), $fileName);
    }

    /**
     * Export to PDF
     */
    public function exportApplicantReportToPDF(Request $request)
    {
        $data = $this->generateApplicantReport($request);
        $reports = $data->toArray();
        
        $pdf = Pdf::loadView('reports.applicant_report', [
            'reports' => $reports,
            'filters' => $request->all(),
            'generated_at' => now()->format('d/m/Y H:i:s')
        ]);
        
        return $pdf->download('applicant_report_' . now()->format('Y_m_d_His') . '.pdf');
    }

    /**
     * Export to CSV
     */
    public function exportApplicantReportToCSV(Request $request)
    {
        $data = $this->generateApplicantReport($request);
        $reports = $data->toArray();
        $fileName = 'applicant_report_' . now()->format('Y_m_d_His') . '.csv';

        return Excel::download(new ReportsExport($reports), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Display report page
     */
    public function ApplicantReport(Request $request)
    {
        $branch = session('branch');
        $allData = $this->generateApplicantReport($request);
        
        // Get filter options
        $courses = Course::all();
        $branches = empty($branch) ? Branch::all() : Branch::where('id', $branch->id)->get();
        $venues = empty($branch) 
            ? Venue::all() 
            : Venue::where('branch_id', $branch->id)->get();

        // Pagination
        $dataCollection = collect($allData);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $request->input('per_page', 20);
        $currentPageItems = $dataCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($dataCollection), $perPage);
        $paginatedItems->setPath($request->url());

        // Calculate summary statistics
        $summary = [
            'total_students' => $dataCollection->unique('applicant_id')->count(),
            'total_enrollments' => $dataCollection->count(),
            'total_revenue' => $dataCollection->sum('paid_amount'),
            'total_balance' => $dataCollection->sum('balance'),
            'total_expected' => $dataCollection->sum('total_fee'),
            'collection_rate' => $dataCollection->sum('total_fee') > 0 
                ? round(($dataCollection->sum('paid_amount') / $dataCollection->sum('total_fee')) * 100, 2) 
                : 0,
        ];

        return view('reports.applicant-report-page', compact(
            'paginatedItems',
            'courses',
            'branches',
            'venues',
            'summary'
        ));
    }

    /**
     * Weekly / Monthly / Quarterly ongoing regional center course report (SRS R2).
     * Displays centre name, course name, teaching method, total students, and full/half/unpaid status counts.
     */
    public function centerCourseReport(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $branchId = $request->input('branch_id');
        $sessionBranch = session('branch');

        if (!$branchId && !empty($sessionBranch)) {
            $branchId = $sessionBranch->id;
        }

        $startDate = match ($period) {
            'weekly' => now()->startOfWeek(),
            'quarterly' => now()->firstOfQuarter(),
            'all' => null,
            default => now()->startOfMonth(),
        };

        $enrollments = Enrollment::with(['course', 'venue.branch', 'applicant', 'invoices'])
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('venue', fn ($vq) => $vq->where('branch_id', $branchId));
            })
            ->when($startDate, fn ($q) => $q->whereDate('start_date', '>=', $startDate->toDateString()))
            ->get();

        $rows = $enrollments
            ->groupBy(fn ($e) => ($e->venue?->branch_id ?? 0) . '-' . ($e->course_id ?? 0))
            ->map(function ($group) {
                $first = $group->first();
                $fullCount = 0;
                $halfCount = 0;
                $unpaidCount = 0;
                $collected = 0.0;
                $outstanding = 0.0;

                foreach ($group as $enrollment) {
                    $invoice = $enrollment->invoices;
                    if (!$invoice || $invoice->status === '0') {
                        $unpaidCount++;
                    } elseif ($invoice->status === '1') {
                        $fullCount++;
                    } else {
                        $halfCount++;
                    }
                    if ($invoice) {
                        $collected += (float) $invoice->paid_amount;
                        $outstanding += (float) $invoice->balance;
                    }
                }

                return [
                    'branch_name' => $first->venue?->branch?->name ?? 'Main Centre',
                    'course_title' => $first->course?->title ?? 'Course',
                    'teaching_method' => $first->course?->teachingMethodLabel() ?? 'Face to Face',
                    'total_students' => $group->count(),
                    'full_paid' => $fullCount,
                    'half_paid' => $halfCount,
                    'unpaid' => $unpaidCount,
                    'collected' => $collected,
                    'outstanding' => $outstanding,
                ];
            })
            ->sortBy([['branch_name', 'asc'], ['course_title', 'asc']])
            ->values();

        if ($request->input('export') === 'csv') {
            return response()->streamDownload(function () use ($rows) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Centre Name', 'Course Name', 'Teaching Method', 'Total Students', 'Full Paid', 'Half / Partial Paid', 'Unpaid', 'Collected (TZS)', 'Outstanding (TZS)']);
                foreach ($rows as $row) {
                    fputcsv($handle, array_values($row));
                }
                fclose($handle);
            }, 'center_course_report_' . $period . '_' . date('Ymd_His') . '.csv');
        }

        if ($request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.center-course-pdf', [
                'rows' => $rows,
                'period' => $period,
                'generatedAt' => now()->format('d/m/Y H:i'),
            ]);
            return $pdf->download('center_course_report_' . $period . '_' . date('Ymd_His') . '.pdf');
        }

        $branches = empty($sessionBranch) ? Branch::orderBy('name')->get() : Branch::where('id', $sessionBranch->id)->get();

        return view('reports.center-course-report', compact('rows', 'period', 'branchId', 'branches'));
    }
}
