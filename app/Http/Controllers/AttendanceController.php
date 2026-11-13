<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\CourseAssignment;
use App\Models\Enrollment;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Manages class session attendance marking and CSV export (SRS R6 Instructor & Coordinator).
 */
class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        if (!PermissionService::can('manage-attendance') && !PermissionService::can('list-courseassignments')) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();
        $assignmentsQuery = CourseAssignment::with(['course', 'branch', 'venue', 'tutor'])->latest('date');

        if ($user->hasRole('tutor') && $user->tutor) {
            $assignmentsQuery->where('tutor_id', $user->tutor->id);
        }

        $assignments = $assignmentsQuery->get();
        $selectedId = $request->get('assignment_id') ?: ($assignments->first()?->id);
        $selectedAssignment = $selectedId ? $assignments->firstWhere('id', (int) $selectedId) : null;

        $enrollments = collect();
        $existingAttendance = collect();

        if ($selectedAssignment) {
            $enrollments = Enrollment::with(['applicant'])
                ->where('course_id', $selectedAssignment->course_id)
                ->where('venue_id', $selectedAssignment->venue_id)
                ->get();

            $existingAttendance = AttendanceRecord::where('course_assignment_id', $selectedAssignment->id)
                ->pluck('status', 'enrollment_id');
        }

        return view('attendance.index', compact('assignments', 'selectedAssignment', 'enrollments', 'existingAttendance'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (!PermissionService::can('manage-attendance') && !PermissionService::can('list-courseassignments')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'course_assignment_id' => ['required', 'exists:course_assignments,id'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'in:present,absent,excused'],
        ]);

        $assignment = CourseAssignment::findOrFail($validated['course_assignment_id']);

        foreach ($validated['attendance'] as $enrollmentId => $status) {
            $enrollment = Enrollment::find($enrollmentId);
            if (!$enrollment) {
                continue;
            }

            AttendanceRecord::updateOrCreate(
                [
                    'course_assignment_id' => $assignment->id,
                    'enrollment_id' => $enrollment->id,
                ],
                [
                    'applicant_id' => $enrollment->applicant_id,
                    'tutor_id' => $assignment->tutor_id,
                    'status' => $status,
                    'marked_at' => now(),
                ]
            );

            $total = AttendanceRecord::where('enrollment_id', $enrollment->id)->count();
            $present = AttendanceRecord::where('enrollment_id', $enrollment->id)
                ->whereIn('status', ['present', 'excused'])
                ->count();
            $pct = $total > 0 ? round(($present / $total) * 100, 2) : 0;
            $enrollment->forceFill(['attendance_percentage' => $pct])->save();
        }

        return back()->with('success', 'Session attendance saved successfully.');
    }

    public function export(Request $request)
    {
        if (!PermissionService::can('manage-attendance') && !PermissionService::can('list-courseassignments')) {
            abort(403, 'Unauthorized');
        }

        $records = AttendanceRecord::with(['courseAssignment.course', 'courseAssignment.branch', 'applicant', 'tutor'])
            ->latest('marked_at')
            ->get();

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Course', 'Centre', 'Instructor', 'Session Date', 'Status', 'Marked At']);
            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->applicant?->full_name ?? '-',
                    $record->courseAssignment?->course?->title ?? '-',
                    $record->courseAssignment?->branch?->name ?? '-',
                    $record->tutor?->name ?? '-',
                    $record->courseAssignment?->date?->format('Y-m-d') ?? '-',
                    ucfirst($record->status),
                    $record->marked_at?->format('Y-m-d H:i') ?? '-',
                ]);
            }
            fclose($handle);
        }, 'attendance_export_' . date('Ymd_His') . '.csv');
    }
}
