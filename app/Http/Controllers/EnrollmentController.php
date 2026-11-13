<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Course;
use Illuminate\View\View;
use App\Models\Applicant;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Traits\SocialSmartTechConstants;
use App\Services\PermissionService;

class EnrollmentController extends Controller
{
    use SocialSmartTechConstants;
    public function index(Request $request)
    {
        if (!PermissionService::can('List enrollments')) {
            abort(403, 'Unauthorized');
        }
        $branch = session('branch');

        $search = $request->get('search', '');

        $enrollments = empty($branch) ?
            Enrollment::search($search)
            ->latest()
            ->paginate(999999999999)
            ->withQueryString() :
            Enrollment::search($search)
            ->whereIn('venue_id',Venue::where('branch_id',$branch->id)->pluck('id'))
            ->latest()
            ->paginate(999999999999)
            ->withQueryString();

        return view('app.enrollments.index', compact('enrollments', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
         if (!PermissionService::canCreate('Add enrollments')) {
            abort(403, 'Unauthorized');
        }
        $branch = session('branch');

        return redirect()->route('enrollments.index')->with('error', 'You are not allowed to create enrollment directly. Please create applicant first and then enroll him/her to a course. Thank you!');

        $applicants = Applicant::pluck('full_name', 'id');
        $courses = Course::pluck('title', 'id');
        $venues = empty($branch) ? Venue::pluck('name', 'id') : Venue::where('branch_id',$branch->id)->pluck('name','id');

        return view(
            'app.enrollments.create',
            compact('applicants', 'courses', 'venues')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
         if (!PermissionService::canCreate('Add enrollments')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'applicant_id' => ['required', 'exists:applicants,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'session' => ['required', 'in:morning,afternoon'],
            'venue_id' => ['required', 'exists:venues,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'fee' => ['nullable', 'numeric'],
            'duration' => ['nullable', 'max:255', 'string'],
        ]);

        $enrollment = Enrollment::create($validated);

        return redirect()
            ->route('enrollments.edit', $enrollment)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Enrollment $enrollment)
    {
        if (!PermissionService::canView('View enrollments', $enrollment)) {
            abort(403, 'Unauthorized');
        }

        return view('app.enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Enrollment $enrollment)
    {
         if (!PermissionService::canUpdate('Update enrollments', $enrollment)) {
            abort(403, 'Unauthorized');
        }
        $branch = session('branch');
        return redirect()->route('enrollments.index')->with('error', 'You are not allowed to edit enrollment directly. Please edit applicant first and then enroll him/her to a course. Thank you!');

        $applicants = Applicant::pluck('full_name', 'id');
        $courses = Course::pluck('title', 'id');
        $venues = empty($branch) ? Venue::pluck('name', 'id') : Venue::where('branch_id',$branch->id)->pluck('name','id');


        return view(
            'app.enrollments.edit',
            compact('enrollment', 'applicants', 'courses', 'venues')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        Enrollment $enrollment
    ): RedirectResponse {
         if (!PermissionService::canUpdate('Update enrollments', $enrollment)) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'applicant_id' => ['required', 'exists:applicants,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'session' => ['required', 'in:morning,afternoon'],
            'venue_id' => ['required', 'exists:venues,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'fee' => ['nullable', 'numeric'],
            'duration' => ['nullable', 'max:255', 'string'],
        ]);

        $enrollment->update($validated);

        return redirect()
            ->route('enrollments.edit', $enrollment)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Enrollment $enrollment
    ) {
        if (!PermissionService::canDelete('Delete enrollments', $enrollment)) {
            abort(403, 'Unauthorized');
        }

        $enrollment->delete();
        echo 1;
    }
}
