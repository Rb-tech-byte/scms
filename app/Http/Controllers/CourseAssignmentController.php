<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Enrollment;
use App\Models\Tutor;
use App\Models\Venue;
use App\Models\Branch;
use App\Models\Course;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\CourseAssignment;
use Illuminate\Http\RedirectResponse;
use App\Traits\SocialSmartTechConstants;

class CourseAssignmentController extends Controller
{
    use SocialSmartTechConstants;
    protected $data = [];

    public function __construct()
    {
        $this->data['title'] = 'Course Assignment';
    }
    /**
     * @author developer006 <email>
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$this->hasPermission('List courseassignments')) {
            return redirect('permission-denied');
        }
        $branch = session('branch');
        $search = $request->get('search', '');

        if (auth()->user()->hasRole('tutor') || !auth()->user()->hasRole(['system-admin', 'branch-admin'])) {
            $tutor_id = auth()->user()->tutor?->id ?? 0;
            $tutor = Tutor::find($tutor_id);
            $this->data['variable'] = 'Tutor ' . ($tutor?->name ?? 'Tutor');
            $this->data['courseAssignments'] = CourseAssignment::where('tutor_id', $tutor_id)->get();
            return view('app.course_assignments.index', $this->data);
        } else {
            $courseAssignments = empty($branch) ? CourseAssignment::latest()->get() : CourseAssignment::where('branch_id', $branch->id ?? $branch)->latest()->get();
        }


        return view(
            'app.course_assignments.index',
            compact('courseAssignments', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!$this->hasPermission('Add courseassignments')) {
            return redirect('permission-denied');
        }
        $branch = session('branch');
        if(!empty($branch)){
        $tutors = Tutor::where('branch_id',$branch->id)->pluck('name', 'id');
        $branches = Branch::where('id',$branch->id)->pluck('name', 'id');
        $venues = Venue::where('branch_id',$branch->id)->pluck('name', 'id');
        }else{

        $tutors = Tutor::pluck('name', 'id');
        $branches = Branch::pluck('name', 'id');
        $venues = Venue::pluck('name', 'id');
        }
        $courses = Course::pluck('title', 'id');
        

        return view(
            'app.course_assignments.create',
            compact('tutors', 'branches', 'venues', 'courses')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$this->hasPermission('Add courseassignments')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'tutor_id' => ['required', 'exists:tutors,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'venue_id' => ['required', 'exists:venues,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    $start_time = $request->input('start_time');
                    if ($start_time >= $value) {
                        $fail('The end time must be after the start time.');
                    }
                },
            ],
            'active' => ['required', 'boolean'],
        ]);

        /**
         * @author Renatus Bernardo <bernadorenatus32@gmail.com>
         * Solve the problem of time overlapping and tutor scheduling
         */

        // Check if the tutor is already assigned to the course for the same date
        $tutor_id = $request->input('tutor_id');
        $tutor = Tutor::find($tutor_id);
        $tutor_name = $tutor?->name ?? 'Tutor';
        $course_id = $request->input('course_id');
        $course = Course::find($course_id);
        $course_title = $course?->title ?? 'Course';
        $date = $request->input('date');
        $courseAssignment = CourseAssignment::where('tutor_id', $tutor_id)->where('course_id', $course_id)->where('date', $date)->first();

        if ($courseAssignment) {
            return redirect()->back()->with('warning', 'Tutor ' . $tutor_name . ' is already assigned to the course ' . $course_title . ' for the same date. "' . \Carbon\Carbon::parse($date)->format('d-m-Y') . '"');

        }

        //check if tutor having another assignment at the range time (start_time to end_time) and same date
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $courseAssignment = CourseAssignment::where('tutor_id', $tutor_id)
            ->where('date', $date)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time]);
            })
            ->first();

        if ($courseAssignment) {
            return redirect()->back()->with('warning', 'Tutor ' . $tutor_name . ' is already assigned to another course ' . $courseAssignment->course->title . ' at the range time (start_time to end_time) and same date.');
        }


        //check if venue ha0ving another assignment at the range time (start_time to end_time) and same date
        $venue_id = $request->input('venue_id');
        $conflictingCourses = CourseAssignment::where('venue_id', $venue_id)
            ->where('date', $date)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time]);
            })
            ->get();

        if ($conflictingCourses->isNotEmpty()) {
            $coursesInfo = $conflictingCourses->map(function ($course) {
                return 'Course: ' . $course->course->title . ', Tutor: ' . $course->tutor->name;
            })->implode(' ');

            return redirect()->back()->with('warning', 'Venue is already assigned to another course at the range time (start_time to end_time) and same date. Conflicting Courses: <br>' . $coursesInfo);
        }

        //check if tutor having 3 assignments at the same date
        $courseAssignment = CourseAssignment::where('tutor_id', $tutor_id)->where('date', $date)->count();
        if ($courseAssignment >= 3) {
            return redirect()->back()->with('warning', 'Tutor ' . $tutor_name . ' is already assigned to 3 courses at the same date.');
        }

        //check if the range time (start_time to end_time) is not less than 30 minutes
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $start_time = \Carbon\Carbon::parse($start_time);
        $end_time = \Carbon\Carbon::parse($end_time);
        $diff = $start_time->diffInMinutes($end_time);
        if ($diff < 30) {
            return redirect()->back()->with('warning', 'The range time (start_time "' . $start_time . '" to end_time  "' . $end_time . '" ) must not be less than 30 minutes.');
        }

        $courseAssignment = CourseAssignment::create($validated);

        return redirect()
            ->route('course-assignments.index')
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Request $request,
        CourseAssignment $courseAssignment
    ) {
        if (!$this->hasPermission('View courseassignments')) {
            return redirect('permission-denied');
        }

        return view('app.course_assignments.show', compact('courseAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        Request $request,
        CourseAssignment $courseAssignment
    ) {
        if (!$this->hasPermission('Update courseassignments')) {
            return redirect('permission-denied');
        }
        $branch = session('branch');
        if(!empty($branch)){
            $tutors = Tutor::where('branch_id',$branch->id)->pluck('name', 'id');
            $branches = Branch::where('id',$branch->id)->pluck('name', 'id');
            $venues = Venue::where('branch_id',$branch->id)->pluck('name', 'id');
            }else{
        $tutors = Tutor::pluck('name', 'id');
        $branches = Branch::pluck('name', 'id');
        $venues = Venue::pluck('name', 'id');
            }
        $courses = Course::pluck('title', 'id');

        return view(
            'app.course_assignments.edit',
            compact(
                'courseAssignment',
                'tutors',
                'branches',
                'venues',
                'courses'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        CourseAssignment $courseAssignment
    ): RedirectResponse {
        if (!$this->hasPermission('Update courseassignments')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'tutor_id' => ['required', 'exists:tutors,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'venue_id' => ['required', 'exists:venues,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s'],
            'active' => ['required', 'boolean'],
        ]);

        $courseAssignment->update($validated);

        return redirect()
            ->route('course-assignments.index')
            ->withSuccess(__('crud.common.saved'));
    }

    public function update_schedule_status($id)
    {
        if (!$this->hasPermission('Update courseassignments')) {
            return redirect('permission-denied');
        }

        $courseAssignment = CourseAssignment::find($id);
        if ($courseAssignment->is_teached == 0) {
            $courseAssignment->is_teached = 1;
            $courseAssignment->active = 0;
            $courseAssignment->save();
        } else {
            return redirect()->back()->with('warning', 'You can not update this schedule status, Schedule is already teached.');
        }

        return redirect()->back()->with('success', 'Schedule status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        CourseAssignment $courseAssignment
    ){
        if (!$this->hasPermission('Delete courseassignments')) {
            return redirect('permission-denied');
        }

        $courseAssignment->delete();
        echo 1;

        // return redirect()
        //     ->route('course-assignments.index')
        //     ->withSuccess(__('crud.common.removed'));
    }

    public function getBranchCourseAssignment(Request $request)
    {
        $user = auth()->user();
        $branch_id = null;
        $applicant = null;

        if ($user->hasRole('tutor')) {
            $branch_id = $user->tutor?->branch_id;
        }
        if ($user->hasRole('student')) {
            $applicant = Applicant::where('user_id', $user->id)->first();
            $branch_id = $applicant?->branch_id;
        }

        $branch = $branch_id ? Branch::find($branch_id) : Branch::first();
        $branch_id = $branch?->id ?? 1;

        $this->data['title'] = 'Branch Timetable';
        $this->data['variable'] = 'Branch ' . ($branch?->name ?? 'General');
        if ($user->hasRole(['tutor', 'branch-admin'])) {
            $this->data['courseAssignments'] = CourseAssignment::where('branch_id', $branch_id)->get();
        }
        if ($user->hasRole('student')) {
            //get all courses enrolled by the student then get all the course assignments for the courses
            $enrollments = $applicant ? Enrollment::where('applicant_id', $applicant->id)->get() : collect();

            $course_ids = [];
            $venues = [];
            foreach ($enrollments as $enrollment) {
                $course_ids[] = $enrollment->course_id;
                $venues[] = $enrollment->venue_id;
            }
            $this->data['courseAssignments'] = (!empty($course_ids) && !empty($venues))
                ? CourseAssignment::whereIn('course_id', $course_ids)->whereIn('venue_id', $venues)->where('branch_id', $branch_id)->get()
                : collect();
        }
        return view('app.course_assignments.index', $this->data);
    }
}
