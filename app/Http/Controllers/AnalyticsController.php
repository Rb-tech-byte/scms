<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CourseAssignment;
use App\Models\Invoice;
use App\Models\Tutor;
use App\Services\PermissionService;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * Provides analytical views and executive operational oversight (SRS R2, R4, R5).
 */
class AnalyticsController extends Controller
{
    public function branches(): View
    {
        if (!PermissionService::can('view-all-reports') && !PermissionService::can('view-dashboard')) {
            abort(403, 'Unauthorized');
        }

        $branches = Branch::with(['applicants.invoices', 'venues'])->orderBy('name')->get()
            ->map(function ($branch) {
                $invoices = $branch->applicants->flatMap->invoices;
                return [
                    'name' => $branch->name,
                    'region' => $branch->location ?? ($branch->region ?: '-'),
                    'venues_count' => $branch->venues->count(),
                    'applicants_count' => $branch->applicants->count(),
                    'collected' => $invoices->sum('paid_amount'),
                    'outstanding' => $invoices->sum('balance'),
                ];
            });

        return view('analytics.branches', compact('branches'));
    }

    public function discounts(): View
    {
        if (!PermissionService::can('view-all-reports') && !PermissionService::can('list-invoices')) {
            abort(403, 'Unauthorized');
        }

        $discountedInvoices = Invoice::with(['applicant.branch', 'enrollment.course'])
            ->where('has_discount', 1)
            ->latest()
            ->get();

        $totalWaived = $discountedInvoices->sum('discount_amount');

        return view('analytics.discounts', compact('discountedInvoices', 'totalWaived'));
    }

    public function tutorWorkload(): View
    {
        if (!PermissionService::can('view-all-reports') && !PermissionService::can('list-tutors')) {
            abort(403, 'Unauthorized');
        }

        $tutors = Tutor::with(['branch', 'courseAssignments.course'])->orderBy('name')->get()
            ->map(function ($tutor) {
                $active = $tutor->courseAssignments->where('is_teached', 0)->count();
                $completed = $tutor->courseAssignments->where('is_teached', 1)->count();
                return [
                    'name' => $tutor->name,
                    'branch' => $tutor->branch?->name ?? '-',
                    'active_sessions' => $active,
                    'completed_sessions' => $completed,
                    'total_sessions' => $active + $completed,
                ];
            });

        return view('analytics.tutor-workload', compact('tutors'));
    }

    /**
     * List all active/ongoing classes and upcoming cohorts (SRS R5 Director).
     */
    public function activeClasses(): View
    {
        if (!PermissionService::can('view-active-classes') && !PermissionService::can('list-courseassignments')) {
            abort(403, 'Unauthorized');
        }

        $classes = CourseAssignment::with(['course', 'branch', 'venue', 'tutor'])
            ->where('active', 1)
            ->where('is_teached', 0)
            ->orderBy('date')
            ->get();

        return view('analytics.active-classes', compact('classes'));
    }
}
