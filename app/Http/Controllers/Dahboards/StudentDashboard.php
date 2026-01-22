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
        $enrollments = Enrollment::where('applicant_id', $applicant->id)->get();

        $course_ids = [];
        $venues = [];
        foreach($enrollments as $enrollment) {
            $course_ids[] = $enrollment->course_id;
            $venues[] = $enrollment->venue_id;
        }
        $this->data['sessions'] = CourseAssignment::whereIn('course_id', $course_ids)->whereIn('venue_id', $venues)->where('branch_id', $applicant->branch_id)->where('is_teached', 0)->get();
        $this->data['teached_sessions'] = CourseAssignment::whereIn('course_id', $course_ids)->whereIn('venue_id', $venues)->where('branch_id', $applicant->branch_id)->where('is_teached', 1)->get();
        $this->data['new_sessions'] = CourseAssignment::whereIn('course_id', $course_ids)->whereIn('venue_id', $venues)->where('branch_id', $applicant->branch_id)->where('is_teached', 0)->get();

        $this->data['messsages'] = Message::where('receiver_id', $user->id)->where('opened', 0)->get();


        return view('dashboards.student', $this->data);
    }
}
