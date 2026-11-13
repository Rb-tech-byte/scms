<?php

use App\Http\Controllers\CourseAssignmentController;
use App\Http\Controllers\PaymentScheduleController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TutorController;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\ForceResetPasswordController;
use App\Http\Controllers\GlobalSearchController;
use App\Models\Setting as SystemSettings;
use Codedge\Fpdf\Fpdf\Fpdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //check system setting

    $settings = SystemSettings::all();
    if (empty ($settings)) {
        $settingData = [
            'system_name ' => 'OPEN UNIVERSITY ONLINE COURSE REGISTRATION',
        ];
        SystemSettings::create($settingData);
    }
    return redirect()
        ->route('login');
});

Route::get('/ViewInvoice/{id}', function ($id) {
    $invoice = Invoice::findOrFail($id);
    $status = $invoice->status;
    $status_color = '52, 71, 209';
    $text_color = '52, 71, 209';
    if ($status == '0') {
        $status_color = '212, 83, 74';
        $text_color = '212, 83, 74';
        $message = 'Unpaid';
    } elseif ($status == '1') {
        $status_color = '21, 214, 92';
        $text_color = '21, 214, 92';
        $message = 'Paid';
    } elseif ($status == '2') {
        $status_color = '230, 206, 30';
        $text_color = '230, 206, 30';
        $message = 'Partially Paid';
    }
    qrcode($invoice->id, 'png');
    list ($r, $g, $b) = explode(', ', $status_color);
    $pdf = new Fpdf('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Invoice Logo
    $pdf->Image(public_path('mini.png'), 10, 10, 40);

    // Invoice Status
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(0, 123, 255);
    $pdf->Cell(40, 10, '', 0);
    $pdf->Cell(40, 10, '', 0);
    $pdf->Cell(50, 10, '#' . $invoice->invoice_prefix, 1, 0, 'C', 1);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor($r, $g, $b);
    $pdf->Cell(50, 10, $message, 1, 1, 'C', 1);

    // Billing Date
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(130);
    $pdf->Cell(40, 10, 'Billing Date: ' . $invoice->date->format('d-m-Y'), 0, 1);

    // Invoice Details
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(20);
    $pdf->Cell(50, 0, '', 0);
    $pdf->Cell(80, 10, '', 0);
    $pdf->Cell(50, 0, '', 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(50, 7, 'Institute of Education and', 0, 0, 'L');
    $pdf->Cell(80, 7, '', 0);
    $pdf->Cell(50, 7, 'Invoice To: ' . $invoice->applicant->full_name, 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(50, 7, 'Management Technologies (IEMT)', 0, 0, 'L');
    $pdf->Cell(80, 7, '', 0);
    $pdf->Cell(50, 7, 'Enrollment Date: ' . $invoice->date->format('d-m-Y'), 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(50, 7, 'P.O.BOX 23409, Kinondoni,', 0, 0, 'L');
    $pdf->Cell(80, 7, '', 0);
    $pdf->Cell(50, 7, 'Invoice Number: ' . $invoice->invoice_prefix, 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(50, 7, 'Dar es Salaam, Tanzania', 0, 0, 'L');
    $pdf->Cell(80, 7, '', 0);
    $pdf->Cell(50, 7, 'Invoice Status: ' . $message, 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(50, 7, '+255 799 888 234 | +255 799 888 233', 0, 0, 'L');
    $pdf->Cell(80, 7);
    $pdf->Cell(50, 7);


    // Table Headers
    $pdf->SetDrawColor(159, 161, 160);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Ln(20);
    $pdf->Cell(10, 10, '#', 1);
    $pdf->Cell(60, 10, 'Course', 1);
    $pdf->Cell(20, 10, 'Quantity', 1);
    $pdf->Cell(50, 10, 'Enrollment Cost (Tsh)', 1);
    $pdf->Cell(40, 10, 'Total', 1, 0, 'C');
    $pdf->Ln();


    // Table Data
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(10, 10, '1.', 1);
    $pdf->Cell(60, 10, $invoice->enrollment->course->title, 1);
    $pdf->Cell(20, 10, '1', 1);
    $pdf->Cell(50, 10, number_format($invoice->enrollment->course->fee, 2, '.', ','), 1);
    $pdf->Cell(40, 10, number_format($invoice->enrollment->course->fee, 2, '.', ',') . ' Tsh', 1);
    $pdf->Ln();

    // Subtotal, Discount, Balance
    $pdf->Ln(10);
    $pdf->Cell(50, 0, '', 0);
    $pdf->Cell(80, 10, '', 0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(130, 129, 129);
    $pdf->Cell(50, 0, 'Sub-total: ' . number_format($invoice->enrollment->course->fee, 2, '.', ',') . ' Tsh', 0, 1, 'R');
    $pdf->Ln(5);
    $pdf->Cell(50, 0, '', 0);
    $pdf->Cell(80, 10, '', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 0, ': ' . number_to_words($invoice->enrollment->course->fee) . '', 0, 0, 'R');
    $pdf->Ln(5);

    $pdf->Cell(50, 0, '', 0);
    $pdf->Image(public_path('qrcodes/qrcode-' . $invoice->id . '.png'), 10, 140, 30);
    $pdf->Cell(80, 10, '', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetTextColor(247, 62, 62);
    $pdf->Cell(50, 10, 'Discount: ' . number_format($invoice->discount_amount, 2, '.', ',') . ' Tsh', 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(50, 0, '', 0);
    $pdf->Cell(80, 10, '', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(50, 0, ': ' . number_to_words($invoice->discount_amount) . '', 0, 0, 'R');
    $pdf->Ln(10);
    $pdf->Cell(50, 0, '', 0);
    $pdf->Cell(80, 10, '', 0);
    $pdf->SetTextColor(56, 55, 55);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, 'Balance: ' . number_format($invoice->balance, 2, '.', ',') . ' Tsh', 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(50, 0, '', 0);
    $pdf->Cell(80, 10, '', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 0, ': ' . number_to_words($invoice->balance) . '', 0, 0, 'R');

    // Notes
    $pdf->Ln(20);
    $pdf->SetLineWidth(0.5);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 180, $pdf->GetY()); // Adjust the length as needed
    $pdf->Cell(0, 10, '---------xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx--------', 0, 1);
    $pdf->MultiCell(0, 10, getSiteSettings()->invoice_footer ?? '');

    // Output PDF
    $pdf->Output('Invoice_' . $invoice->invoice_prefix . '.pdf', 'I');
    //$pdf->Output();
});

Route::get('force-password-reset', [ForceResetPasswordController::class, 'showForm'])->name('password.force_reset');
Route::post('force-password-reset', [ForceResetPasswordController::class, 'update'])->name('password.force_reset.update');
Auth::routes();

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'branch.access'])->name('home');

Route::get('/no-branch', function () {
    return view('dashboards.no-dashboard', [
        'reason' => 'Your account is not assigned to any branch yet. Please contact the System Admin to assign your branch.',
    ]);
})->middleware(['auth'])->name('no-branch');

Route::get('v1/countries/search', [ApiController::class, 'search'])->name('countries.search');
Route::get('/get-course-fee/{selectedCourseId}', [ApiController::class, 'fetchCourseFees'])->name('fees.get');
Route::get('/get-branch/{tutor_id}', [ApiController::class, 'fetch_branch_by_tutor_id']);
Route::get('/get-venue/{branch_id}', [ApiController::class, 'fetch_venue_by_branch_id']);


Route::get('/get-courses', [ApiController::class, 'fetchCourse']);
Route::get('/get-venues', [ApiController::class, 'fetchVenue']);
Route::get('/get-branches', [ApiController::class, 'fetchBranch']);

//check if there is SystemSettings record in a system if found 0 record create record with system_name



Route::prefix('/')
    ->middleware(['auth', 'branch.access'])
    ->group(function () {
        route::get('permission-denied', function () {
            abort(403);
        })->name('permission-denied');
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::get('permission/manage/{role_id?}', [PermissionController::class,'manage_permission'])->name('permission.manage');
        Route::post('permission/assign-role/', [PermissionController::class,'assignPermissionRole'])->name('permission.assign_role_permissions');
        Route::post('encrypt/role/', [PermissionController::class,'encryptRole'])->name('encrypt.role');
        Route::resource('branches', BranchController::class);
        Route::resource('venues', VenueController::class);
        Route::resource('applicants', ApplicantController::class);
        Route::resource('payments', PaymentController::class);
        Route::resource('invoices', InvoiceController::class);
        Route::get('partial/invoice', [InvoiceController::class, 'partial_invoice'])->name('invoice.partial');
        Route::get('paid/invoice', [InvoiceController::class, 'paid_invoice'])->name('invoice.paid');
        Route::resource('notifications', NotificationController::class);
        Route::put('user/updatePermissions/{user_id}', [UserController::class, 'updatePermissions'])->name('user.updatePermissions');
        Route::resource('users', UserController::class);
        Route::resource('countries', CountryController::class);
        Route::resource('enrollments', EnrollmentController::class);
        Route::resource('courses', CourseController::class);
        Route::resource('settings', SettingController::class);
        Route::resource('tutors', TutorController::class);
        Route::get('/view-tutor/{id}', [TutorController::class, 'view_tutor'])->name('view.tutor');
        Route::resource('course-assignments', CourseAssignmentController::class);
        Route::get('update-assigned-course-teached-status/{course_id}', [CourseAssignmentController::class, 'update_schedule_status'])->name('update-teached.course');
        Route::get('system-logs', [SettingController::class, 'show_system_logs'])->name('system.logs');
        Route::get('system-logs-clear', [SettingController::class, 'clear_system_logs'])->name('system.logs.clear');
        Route::post('filtercourse', [PaymentController::class, 'getCourse'])->name('payment.filter_course');
        Route::post('course_amount', [PaymentController::class, 'getCourseAmount'])->name('payment.course_amount');
        Route::get('branch-course-assignment', [CourseAssignmentController::class, 'getBranchCourseAssignment'])->name('course-assignment.branch');
        Route::get('redirect-back', function () {
            return redirect()->back();
        })->name('redirect.back');

        Route::get('generate-invoice-pdf/{id}', [InvoiceController::class, 'generate_invoice_pdf'])->name('invoice_pdf.generate');
        Route::get('message/{id}', [NotificationController::class, 'show_message'])->name('message.show');
        Route::get('messages', [NotificationController::class, 'show_all_messages'])->name('message.show_all');

        //TUTOR ROUTES
        Route::get('/t-upcoming-session', [TutorController::class, 'upcoming_session'])->name('tutor.upcoming_session');
        Route::get('/t-completed-session', [TutorController::class, 'completed_session'])->name('tutor.completed_session');

        //REPORTS
        Route::get('/export-applicant-report-to-pdf', [ReportController::class,'exportApplicantReportToPDF']);
        Route::get('/export-applicant-report-to-excel', [ReportController::class,'exportApplicantReportToExcel']);
        Route::get('/export-applicant-report-to-csv', [ReportController::class,'exportApplicantReportToCSV']);
        Route::get('/view-applicant-report/{query?}', [ReportController::class,'ApplicantReport'])->name('applicant.report');
        Route::post('/global-search', [GlobalSearchController::class, 'search'])->name('global.search');

        // PAYMENT SCHEDULES
        Route::get('payment-schedules', [PaymentScheduleController::class, 'index'])->name('payment-schedules.index');
        Route::get('payment-schedules/create', [PaymentScheduleController::class, 'create'])->name('payment-schedules.create');
        Route::post('payment-schedules/generate', [PaymentScheduleController::class, 'generate'])->name('payment-schedules.generate');
        Route::get('payment-schedules/{invoiceId}', [PaymentScheduleController::class, 'show'])->name('payment-schedules.show');
        Route::put('payment-schedules/{schedule}', [PaymentScheduleController::class, 'update'])->name('payment-schedules.update');
        Route::delete('payment-schedules/{schedule}', [PaymentScheduleController::class, 'destroy'])->name('payment-schedules.destroy');
        Route::post('payment-schedules/{schedule}/mark-sent', [PaymentScheduleController::class, 'markAsSent'])->name('payment-schedules.mark-sent');
        Route::post('payment-schedules/{schedule}/cancel', [PaymentScheduleController::class, 'cancel'])->name('payment-schedules.cancel');
        Route::get('payment-reminders', [PaymentScheduleController::class, 'reminders'])->name('payment-schedules.reminders');
        Route::post('payment-reminders/send', [PaymentScheduleController::class, 'sendReminders'])->name('payment-schedules.send-reminders');

        // CERTIFICATES (stubs)
        Route::get('certificates', [CertificateController::class, 'index']);
        Route::get('certificates/verify', [CertificateController::class, 'verify']);

        // ATTENDANCE (stubs)
        Route::get('attendance', [AttendanceController::class, 'index']);
        Route::get('attendance/export', [AttendanceController::class, 'export']);

        // ANALYTICS (stubs)
        Route::get('analytics/branches', [AnalyticsController::class, 'branches']);
        Route::get('analytics/discounts', [AnalyticsController::class, 'discounts']);
        Route::get('analytics/tutor-workload', [AnalyticsController::class, 'tutorWorkload']);

        // NOTIFICATION TEMPLATES (stubs)
        Route::get('notifications/templates', [NotificationTemplateController::class, 'index']);

    });
