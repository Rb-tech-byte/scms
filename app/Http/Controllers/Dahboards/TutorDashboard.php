<?php

namespace App\Http\Controllers\Dahboards;

use App\Http\Controllers\Controller;
use App\Models\CourseAssignment;
use App\Models\Message;
use App\Models\Tutor;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Applicant;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;

class TutorDashboard extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware(["auth","tutor"]);
    }

    public function index(): View
    {
        $user = auth()->user();
        $tutor = Tutor::where('user_id', $user->id)->first();
        $branch = $tutor?->branch ?? $user->tutor?->branch ?? \App\Models\Branch::first();
        $branchId = $tutor?->branch_id ?? $branch?->id ?? 1;

        $this->data['students'] = Applicant::join('enrollments', 'applicants.id', '=', 'enrollments.applicant_id')
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->join('venues', 'venues.id', '=', 'enrollments.venue_id')
            ->join('branches', 'branches.id', '=', 'venues.branch_id')
            ->where('venues.branch_id', '=', $branchId)
            ->select('applicants.*', 'courses.title as course_name', 'venues.name as venue_name', 'branches.name as branch_name', 'branches.id as branch_id')
            ->get();

        $this->data['courses'] = Course::all();
        $this->data['invoices'] = Invoice::join('enrollments', 'enrollments.id', '=', 'invoices.enrollment_id')
            ->join('applicants', 'applicants.id', '=', 'enrollments.applicant_id')
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->join('venues', 'venues.id', '=', 'enrollments.venue_id')
            ->join('branches', 'branches.id', '=', 'venues.branch_id')
            ->where('venues.branch_id', '=', $branchId)
            ->select('invoices.*', 'applicants.full_name as applicant_name', 'courses.title as course_name', 'venues.name as venue_name', 'branches.name as branch_name')
            ->get();

        $payments = Payment::pluck('amount')->toArray();
        $this->data['payments'] = $payments;
        $this->data['total_payments'] = array_sum($payments);

        $balances = Invoice::pluck('balance')->toArray();
        $this->data['balances'] = $balances;
        $this->data['total_balances'] = array_sum($balances);

        $tutorId = $tutor?->id ?? 0;
        $this->data['sessions'] = CourseAssignment::where('tutor_id', $tutorId)
            ->where('is_teached', 0)->get();
        $this->data['teached_sessions'] = CourseAssignment::where('tutor_id', $tutorId)
            ->where('is_teached', 1)->get();
        $this->data['count_new_messages'] = Message::where('receiver_id', $user->id)->where('opened', 0)->count();
        $this->data['ready_certificates_count'] = \App\Models\Certificate::where('branch_id', $branchId)
            ->whereIn('status', ['ready', 'issued'])
            ->count();

        return view('dashboards.tutor', $this->data);
    }


}
