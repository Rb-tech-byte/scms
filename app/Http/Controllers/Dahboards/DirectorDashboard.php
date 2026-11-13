<?php

namespace App\Http\Controllers\Dahboards;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * Executive dashboard for the Director role (R5): active classes and ready certificates.
 */
class DirectorDashboard extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'branch.access']);
    }

    public function index(): View
    {
        $activeClasses = CourseAssignment::with(['course', 'branch', 'venue', 'tutor'])
            ->where('active', 1)
            ->where('is_teached', 0)
            ->orderBy('date')
            ->limit(15)
            ->get();

        $ongoingEnrollmentsCount = Enrollment::whereDate('end_date', '>=', Carbon::today())->count();
        $readyCertificates = Certificate::with(['applicant', 'course', 'branch'])
            ->whereIn('status', ['ready', 'issued'])
            ->latest('ready_at')
            ->limit(15)
            ->get();

        return view('dashboards.director', [
            'activeClassesCount' => CourseAssignment::where('active', 1)->where('is_teached', 0)->count(),
            'ongoingEnrollmentsCount' => $ongoingEnrollmentsCount,
            'readyCertificatesCount' => Certificate::where('status', 'ready')->count(),
            'issuedCertificatesCount' => Certificate::where('status', 'issued')->count(),
            'branchesCount' => Branch::count(),
            'coursesCount' => Course::count(),
            'activeClasses' => $activeClasses,
            'readyCertificates' => $readyCertificates,
        ]);
    }
}

