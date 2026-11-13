<?php

namespace App\Http\Controllers\Dahboards;

use App\Http\Controllers\Controller;
use App\Models\CourseAssignment;
use App\Models\Enrollment;
use App\Models\Message;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Applicant;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;

use App\Models\PaymentSchedule;
use Carbon\Carbon;

class StudentDashboard extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware(["auth"]);
    }

    public function index() {
        $user = auth()->user();
        $applicant = Applicant::where('user_id', $user->id)->select('id', 'full_name', 'branch_id', 'user_id')->first();
        if (!$applicant) {
            $this->data['sessions'] = collect();
            $this->data['teached_sessions'] = collect();
            $this->data['new_sessions'] = collect();
            $this->data['enrollments'] = collect();
            $this->data['overdue_schedules'] = collect();
            $this->data['messsages'] = Message::where('receiver_id', $user->id)->where('opened', 0)->get();
            return view('dashboards.student', $this->data);
        }

        $enrollments = Enrollment::with(['course', 'venue.branch', 'invoices.payments'])
            ->where('applicant_id', $applicant->id)
            ->latest()
            ->get();

        $course_ids = $enrollments->pluck('course_id')->all();
        $venues = $enrollments->pluck('venue_id')->all();

        $this->data['enrollments'] = $enrollments;
        $this->data['sessions'] = CourseAssignment::whereIn('course_id', $course_ids)->whereIn('venue_id', $venues)->where('branch_id', $applicant->branch_id)->where('is_teached', 0)->get();
        $this->data['teached_sessions'] = CourseAssignment::whereIn('course_id', $course_ids)->whereIn('venue_id', $venues)->where('branch_id', $applicant->branch_id)->where('is_teached', 1)->get();
        $this->data['new_sessions'] = $this->data['sessions'];
        $this->data['overdue_schedules'] = PaymentSchedule::with(['invoice.enrollment.course'])
            ->whereHas('invoice', fn ($q) => $q->where('applicant_id', $applicant->id))
            ->whereIn('status', ['pending', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->get();

        $this->data['messsages'] = Message::where('receiver_id', $user->id)->where('opened', 0)->get();

        return view('dashboards.student', $this->data);
    }
}
