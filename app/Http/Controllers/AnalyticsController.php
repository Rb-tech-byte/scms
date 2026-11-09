<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class AnalyticsController
 *
 * Provides analytical dashboards, executive metrics, and operational reports.
 *
 * METRICS & DEVELOPER GUIDE:
 * 1. Branch Analytics (`branches` action):
 *    - Cross-branch revenue comparisons (aggregating payments grouped by branch).
 *    - Enrollment density per study centre and classroom venue capacity utilization.
 *    - Query tip:
 *      Branch::withCount('applicants')
 *            ->with(['applicants.payments' => fn($q) => $q->selectRaw('applicant_id, sum(amount) as total_rev')->groupBy('applicant_id')])
 *            ->get();
 *
 * 2. Discount & Subsidy Audit (`discounts` action):
 *    - Reports institutional fee waivers granted under staff dependent and out-student policies.
 *    - Tracks total discounted revenue vs gross expected revenue.
 *    - Verifies presence of mandatory discount audit attachments.
 *
 * 3. Tutor Workload Tracking (`tutorWorkload` action):
 *    - Tracks active and completed teaching hours per tutor across courses and venues.
 *    - Balances instructor assignments to prevent scheduling overloads.
 */
class AnalyticsController extends Controller
{
    /**
     * Display comparative performance metrics and enrollment statistics across all branches.
     *
     * @return \Illuminate\View\View
     */
    public function branches()
    {
        return view('analytics.branches');
    }

    /**
     * Display fee discount distribution and financial waiver compliance reports.
     *
     * @return \Illuminate\View\View
     */
    public function discounts()
    {
        return view('analytics.discounts');
    }

    /**
     * Display instructor session allocations, course assignments, and teaching hours.
     *
     * @return \Illuminate\View\View
     */
    public function tutorWorkload()
    {
        return view('analytics.tutor-workload');
    }
}
