<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Certificate;
use App\Models\CourseAssignment;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\Notification as SystemNotification;
use App\Models\NotificationTemplate;
use App\Services\PermissionService;
use App\Services\SrsWorkflowService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Manages course completion certificates, instructor ready-certificate alerts,
 * PDF certificate generation with QR codes, and public serial verification (SRS R2, R5, R6).
 */
class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        if (!PermissionService::can('issue-certificate')
            && !PermissionService::can('view-ready-certificates')
            && !PermissionService::can('clear-applicant')) {
            abort(403, 'Unauthorized');
        }

        $branchId = session('user_branch_id');
        $statusFilter = $request->get('status', 'all');

        $eligibleEnrollments = SrsWorkflowService::getEligibleEnrollments($branchId);
        foreach ($eligibleEnrollments as $enrollment) {
            SrsWorkflowService::ensureCertificate($enrollment, 'ready');
        }

        $certificates = Certificate::with(['applicant', 'course', 'branch', 'enrollment'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when(in_array($statusFilter, ['pending', 'ready', 'issued'], true), fn ($q) => $q->where('status', $statusFilter))
            ->latest('ready_at')
            ->get();

        return view('certificates.index', compact('certificates', 'statusFilter'));
    }

    /**
     * Mark eligible certificates ready and notify course instructors by email & in-app message (SRS R2 / R6).
     */
    public function notifyTutors(Request $request): RedirectResponse
    {
        if (!PermissionService::can('send-certificate-alerts') && !PermissionService::can('issue-certificate')) {
            abort(403, 'Unauthorized');
        }

        $branchId = session('user_branch_id');
        $certificateIds = $request->input('certificate_ids', []);

        $query = Certificate::with(['course', 'branch'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when(!empty($certificateIds), fn ($q) => $q->whereIn('id', $certificateIds))
            ->whereIn('status', ['pending', 'ready']);

        $certificates = $query->get();
        if ($certificates->isEmpty()) {
            return back()->with('warning', 'No pending or ready certificates selected for notification.');
        }

        $template = NotificationTemplate::where('key', 'certificates_ready_tutor')->where('is_active', true)->first();
        $notifiedTutors = 0;

        $grouped = $certificates->groupBy(fn ($c) => $c->course_id . '-' . $c->branch_id);
        foreach ($grouped as $group) {
            $first = $group->first();
            $assignments = CourseAssignment::with('tutor.user')
                ->where('course_id', $first->course_id)
                ->where('branch_id', $first->branch_id)
                ->get();

            $tutors = $assignments->pluck('tutor')->filter()->unique('id');
            foreach ($tutors as $tutor) {
                $body = $template
                    ? $template->render([
                        'tutor_name' => $tutor->name,
                        'course_title' => $first->course?->title ?? 'Course',
                        'branch_name' => $first->branch?->name ?? 'Centre',
                        'ready_count' => $group->count(),
                    ])
                    : "Dear {$tutor->name}, {$group->count()} certificate(s) for {$first->course?->title} ({$first->branch?->name}) are ready.";

                SystemNotification::create([
                    'user_id' => $tutor->user_id,
                    'message' => $body,
                ]);

                if ($tutor->user_id) {
                    Message::create([
                        'sender_id' => Auth::id(),
                        'receiver_id' => $tutor->user_id,
                        'message' => $body,
                        'opened' => 0,
                    ]);
                }

                if (!empty($tutor->email)) {
                    try {
                        Mail::to($tutor->email)->send(new SendMail($body, $template?->subject ?: 'Certificates Ready Alert'));
                    } catch (\Throwable $e) {
                        // Local mail server optional
                    }
                }
                $notifiedTutors++;
            }

            foreach ($group as $cert) {
                $cert->update([
                    'status' => 'ready',
                    'ready_at' => $cert->ready_at ?? now(),
                    'notified_at' => now(),
                ]);
            }
        }

        activity('certificate')
            ->causedBy(Auth::user())
            ->log("Sent certificate-ready alert for {$certificates->count()} certificate(s) to {$notifiedTutors} instructor(s)");

        return back()->with('success', "Marked {$certificates->count()} certificate(s) ready and notified {$notifiedTutors} instructor(s).");
    }

    /**
     * Issue & download an official PDF certificate with QR verification code.
     */
    public function downloadPdf(Certificate $certificate)
    {
        if (!PermissionService::can('issue-certificate')
            && !PermissionService::can('view-ready-certificates')
            && (int) $certificate->applicant?->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($certificate->status !== 'issued') {
            $certificate->update([
                'status' => 'issued',
                'ready_at' => $certificate->ready_at ?? now(),
                'issued_at' => now(),
                'issued_by' => Auth::id(),
            ]);
        }

        $verifyUrl = route('certificates.verify', ['serial' => $certificate->serial_number]);
        $qrSvg = base64_encode(QrCode::format('svg')->size(110)->generate($verifyUrl));

        $certificate->load(['applicant', 'course', 'branch', 'enrollment']);
        $pdf = Pdf::loadView('certificates.pdf', compact('certificate', 'qrSvg', 'verifyUrl'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("Certificate_{$certificate->serial_number}.pdf");
    }

    /**
     * Public certificate verification endpoint by serial number.
     */
    public function verify(Request $request): View
    {
        $serial = trim((string) $request->get('serial', ''));
        $certificate = $serial !== ''
            ? Certificate::with(['applicant', 'course', 'branch', 'enrollment'])
                ->where('serial_number', $serial)
                ->first()
            : null;

        return view('certificates.verify', compact('serial', 'certificate'));
    }
}
