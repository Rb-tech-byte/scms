<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Traits\SocialSmartTechConstants;

class CourseController extends Controller
{
    use SocialSmartTechConstants;
    public function index(Request $request)
    {
        if (!$this->hasPermission('List courses')) {
            return redirect('permission-denied');
        }

        $search = $request->get('search', '');
        $branch = session('branch');

        $courses = Course::search($search)
            ->latest()
            ->paginate(999999999999)
            ->withQueryString();

        return view('app.courses.index', compact('courses', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
         if (!$this->hasPermission('Add courses')) {
            return redirect('permission-denied');
        }

        return view('app.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
         if (!$this->hasPermission('Add courses')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'title' => ['required', 'max:255', 'string'],
            'fee' => ['required', 'numeric'],
            'duration' => ['required', 'in:1,2,3,4,5,6,7,8,9,10'],
            'has_discount' => ['required', 'boolean'],
            'discount' => ['nullable', 'numeric'],
        ]);

        // $validated['title'] = ucfirst(strtolower($validated['title']));
        Course::create($validated);

        return redirect()
            ->route('courses.index')->with('success', 'Course created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Course $course)
    {
        if (!$this->hasPermission('View courses')) {
            return redirect('permission-denied');
        }

        return view('app.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Course $course)
    {
         if (!$this->hasPermission('Update courses')) {
            return redirect('permission-denied');
        }

        return view('app.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
         if (!$this->hasPermission('Update courses')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'title' => ['required', 'max:255', 'string'],
            'fee' => ['required', 'numeric'],
            'duration' => ['required', 'in:1,2,3,4,5,6,7,8,9,10'],
            'has_discount' => ['required', 'boolean'],
            'discount' => ['nullable', 'numeric'],
        ]);

        // $validated['title'] = ucfirst(strtolower($validated['title']));
        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'Course updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Course $course): RedirectResponse
    {
        if (!$this->hasPermission('Delete courses')) {
            return redirect('permission-denied');
        }

        $course->delete();

        return redirect()
            ->route('courses.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
