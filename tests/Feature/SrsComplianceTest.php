<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\NotificationTemplate;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\Role;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Venue;
use App\Services\SrsWorkflowService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SrsComplianceTest extends TestCase
{
    use DatabaseTransactions;

    private function createUserWithRole(string $roleName, array $overrides = []): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $branchId = Branch::value('id') ?? 1;

        return User::create(array_merge([
            'name' => "Test {$roleName}",
            'email' => uniqid($roleName . '_', true) . '@example.test',
            'password' => Hash::make('StrongPass@2026!'),
            'user_type' => $roleName === 'student' ? 'student' : 'staff',
            'davinci_role_id' => $role->id,
            'branch_id' => $roleName === 'student' ? $branchId : ($roleName === 'branch-admin' || $roleName === 'tutor' ? $branchId : null),
            'force_password_reset' => false,
            'password_changed_at' => now(),
            'is_blocked' => false,
        ], $overrides));
    }

    public function test_failed_login_attempts_lockout_after_four_and_block_after_two_lockouts(): void
    {
        $user = $this->createUserWithRole('student');

        for ($i = 1; $i <= 4; $i++) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'WrongPassword@1',
            ]);
            $response->assertSessionHasErrors('email');
        }

        $user->refresh();
        $this->assertEquals(1, $user->lockout_count);
        $this->assertEquals(0, $user->failed_login_attempts);
        $this->assertTrue($user->isLockedOut());

        // Clear temp cooldown and trigger 2nd lockout -> account becomes blocked
        $user->forceFill(['locked_until' => now()->subMinute()])->save();

        for ($i = 1; $i <= 4; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'WrongPassword@1',
            ]);
        }

        $user->refresh();
        $this->assertEquals(2, $user->lockout_count);
        $this->assertTrue($user->is_blocked);
    }

    public function test_expired_password_forces_password_reset_and_concurrent_session_logs_out_old_session(): void
    {
        $user = $this->createUserWithRole('student', [
            'password_changed_at' => now()->subMonths(4),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'StrongPass@2026!',
        ]);
        $response->assertRedirect(route('password.force_reset'));

        // Simulate concurrent login from a second browser updating active_session_id
        $user->refresh();
        $user->forceFill([
            'force_password_reset' => false,
            'password_changed_at' => now(),
            'active_session_id' => 'newer-session-id-xyz',
        ])->save();

        $this->actingAs($user)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->get('/dashboard')
            ->assertRedirect(route('login'));
    }

    public function test_idle_timeout_expires_session_after_five_minutes(): void
    {
        $user = $this->createUserWithRole('student', [
            'active_session_id' => null,
        ]);

        $this->actingAs($user)
            ->withSession(['last_activity_ts' => now()->subMinutes(7)->timestamp])
            ->get('/help')
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_search_filter_and_block_unblock_user(): void
    {
        $admin = User::where('email', 'admin@scms.out.co.tz')->firstOrFail();
        $target = $this->createUserWithRole('student', ['name' => 'Zebra Filter Target']);

        $this->actingAs($admin)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->get('/users?search=Zebra&user_type=student')
            ->assertOk()
            ->assertSee('Zebra Filter Target');

        $this->actingAs($admin)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->post(route('users.toggle-block', $target))
            ->assertRedirect();

        $this->assertTrue($target->fresh()->is_blocked);

        $this->actingAs($admin)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->post(route('users.toggle-block', $target))
            ->assertRedirect();

        $this->assertFalse($target->fresh()->is_blocked);
    }

    public function test_student_can_enroll_generate_control_number_upload_receipt_view_receipt_and_unenroll(): void
    {
        $student = $this->createUserWithRole('student');
        $course1 = Course::create([
            'title' => 'SRS Self Enroll Course',
            'fee' => 150000,
            'duration' => '2',
            'teaching_method' => 'both',
            'has_discount' => 0,
        ]);
        $course2 = Course::create([
            'title' => 'SRS Unenroll Course',
            'fee' => 100000,
            'duration' => '1',
            'teaching_method' => 'online',
            'has_discount' => 0,
        ]);
        $venue = Venue::firstOrFail();

        $this->actingAs($student)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->post(route('student.enroll'), [
                'course_id' => $course1->id,
                'venue_id' => $venue->id,
                'session' => 'morning',
                'start_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('student.portal'));

        $enrollment1 = Enrollment::where('course_id', $course1->id)->latest('id')->firstOrFail();
        $invoice1 = $enrollment1->invoices;
        $this->assertNotNull($invoice1);

        $this->actingAs($student)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->post(route('invoices.control-number', $invoice1))
            ->assertRedirect();

        $invoice1->refresh();
        $this->assertNotEmpty($invoice1->control_number);

        $fakePng = UploadedFile::fake()->image('receipt.png', 200, 200);
        $payResponse = $this->actingAs($student)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->post(route('student.invoices.submit-payment', $invoice1), [
                'amount' => 150000,
                'payment_method' => 'control_number',
                'transaction_reference' => 'GEPG-TEST-9988',
                'receipt_file' => $fakePng,
            ]);

        $payment = Payment::where('invoice_id', $invoice1->id)->latest('id')->firstOrFail();
        $payResponse->assertRedirect(route('payments.receipt', $payment));

        // Remove physical uploaded test artifact from public storage
        if ($payment->receipt_file && file_exists(storage_path('app/public/receipts/' . $payment->receipt_file))) {
            @unlink(storage_path('app/public/receipts/' . $payment->receipt_file));
        }

        $this->actingAs($student)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->get(route('payments.receipt', $payment))
            ->assertOk()
            ->assertSee($payment->receipt_number);

        // Enroll and immediately unenroll a 2nd unpaid course
        $this->actingAs($student)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->post(route('student.enroll'), [
                'course_id' => $course2->id,
                'venue_id' => $venue->id,
                'session' => 'afternoon',
                'start_date' => now()->toDateString(),
            ]);

        $enrollment2 = Enrollment::where('course_id', $course2->id)->latest('id')->firstOrFail();
        $this->actingAs($student)
            ->withSession(['last_activity_ts' => now()->timestamp])
            ->delete(route('student.unenroll', $enrollment2))
            ->assertRedirect();

        $this->assertNull(Enrollment::find($enrollment2->id));
    }

    public function test_director_marketing_tutor_reports_certificates_attendance_and_help_routes_work(): void
    {
        $director = User::where('email', 'director@scms.out.co.tz')->firstOrFail();
        $marketing = User::where('email', 'marketing@scms.out.co.tz')->firstOrFail();
        $tutorUser = User::where('email', 'tutor@scms.out.co.tz')->firstOrFail();
        $admin = User::where('email', 'admin@scms.out.co.tz')->firstOrFail();

        $this->actingAs($director)->withSession(['last_activity_ts' => now()->timestamp])->get('/dashboard')->assertOk();
        $this->actingAs($director)->withSession(['last_activity_ts' => now()->timestamp])->get('/active-classes')->assertOk();
        $this->actingAs($director)->withSession(['last_activity_ts' => now()->timestamp])->get('/certificates?status=ready')->assertOk();

        $this->actingAs($marketing)->withSession(['last_activity_ts' => now()->timestamp])->get('/dashboard')->assertOk();
        $this->actingAs($marketing)->withSession(['last_activity_ts' => now()->timestamp])->get('/reports/center-courses?period=quarterly')->assertOk();

        $this->actingAs($tutorUser)->withSession(['last_activity_ts' => now()->timestamp])->get('/dashboard')->assertOk();
        $this->actingAs($tutorUser)->withSession(['last_activity_ts' => now()->timestamp])->get('/attendance')->assertOk();

        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])->get('/analytics/branches')->assertOk();
        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])->get('/analytics/discounts')->assertOk();
        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])->get('/analytics/tutor-workload')->assertOk();
        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])->get('/notifications/templates')->assertOk();
        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])->get('/help')->assertOk();

        // Verify public certificate serial verification endpoint as an unauthenticated guest
        auth()->logout();
        $cert = Certificate::first();
        if (!$cert) {
            $enrollment = Enrollment::firstOrFail();
            $cert = SrsWorkflowService::ensureCertificate($enrollment, 'ready');
        }
        $this->get(route('certificates.verify', ['serial' => $cert->serial_number]))
            ->assertOk()
            ->assertSee($cert->serial_number);
    }

    public function test_courses_teaching_method_attendance_post_certificates_notify_and_payment_reminders(): void
    {
        $admin = User::where('email', 'admin@scms.out.co.tz')->firstOrFail();
        $tutorUser = User::where('email', 'tutor@scms.out.co.tz')->firstOrFail();
        $enrollment = Enrollment::firstOrFail();

        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])
            ->post('/courses', [
                'title' => 'SRS Blended Course Test',
                'fee' => 250000,
                'duration' => '4',
                'teaching_method' => 'both',
                'has_discount' => 0,
            ])
            ->assertRedirect();

        $createdCourse = Course::where('title', 'SRS Blended Course Test')->firstOrFail();
        $this->assertEquals('both', $createdCourse->teaching_method);

        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])
            ->put("/courses/{$createdCourse->id}", [
                'title' => 'SRS Online Updated Course',
                'fee' => 200000,
                'duration' => '4',
                'teaching_method' => 'online',
                'has_discount' => 0,
            ])
            ->assertRedirect();

        $this->assertEquals('online', $createdCourse->fresh()->teaching_method);

        $tutorProfile = $tutorUser->tutor ?: Tutor::firstOrFail();
        $branchId = $tutorProfile->branch_id ?? $tutorUser->branch_id ?? Branch::value('id');
        $venue = Venue::where('branch_id', $branchId)->first() ?: Venue::firstOrFail();
        $assignment = CourseAssignment::create([
            'branch_id' => $branchId,
            'venue_id' => $venue->id,
            'course_id' => $createdCourse->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'tutor_id' => $tutorProfile->id,
            'active' => true,
            'is_teached' => 0,
        ]);

        $branchEnrollment = Enrollment::whereHas('venue', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->first() ?: Enrollment::create([
            'course_id' => $createdCourse->id,
            'venue_id' => $venue->id,
            'session' => 'morning',
            'applicant_id' => $enrollment->applicant_id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addWeeks(4)->toDateString(),
            'fee' => 200000,
            'duration' => '4',
            'enrollment_status' => 'active',
        ]);

        $this->actingAs($tutorUser)->withSession(['last_activity_ts' => now()->timestamp])
            ->post(route('attendance.store'), [
                'course_assignment_id' => $assignment->id,
                'attendance' => [$branchEnrollment->id => 'present'],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_records', [
            'course_assignment_id' => $assignment->id,
            'enrollment_id' => $branchEnrollment->id,
            'status' => 'present',
        ]);

        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])
            ->post(route('certificates.notify_tutors'))
            ->assertRedirect();

        $sampleInvoice = Invoice::firstOrFail();
        $schedule = PaymentSchedule::create([
            'invoice_id' => $sampleInvoice->id,
            'installment_number' => 99,
            'due_date' => now()->subDays(5)->toDateString(),
            'amount' => 50000,
            'status' => PaymentSchedule::STATUS_PENDING,
        ]);

        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])
            ->get(route('payment-schedules.reminders'))
            ->assertOk()
            ->assertSee('Print Overdue Alerts');

        $this->actingAs($admin)->withSession(['last_activity_ts' => now()->timestamp])
            ->post(route('payment-schedules.send-reminders'), [
                'schedule_ids' => [$schedule->id],
            ])
            ->assertRedirect();
    }
}

