<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class NotificationTemplateController
 *
 * Manages dynamic SMS and email notification templates with variable placeholders.
 *
 * TEMPLATING ARCHITECTURE FOR FUTURE DEVELOPERS:
 * 1. Placeholders Supported in Templates:
 *    - `{student_name}`: Full name of the applicant or student.
 *    - `{course_title}`: Name of the short course.
 *    - `{invoice_number}`: The invoice prefix (e.g. OUT104ABC9X2).
 *    - `{amount_paid}`: Transaction amount formatted with currency.
 *    - `{balance}`: Outstanding balance.
 *    - `{due_date}`: Installment due date.
 *    - `{venue_name}`: Physical training centre or classroom venue.
 *
 * 2. Supported Channels:
 *    - SMS: NextSMS / Beem / Twilio dispatch via `validatePhoneNumber()` in `app/Helpers/Helpers.php`.
 *    - Email: Standard Laravel Mailable notifications via `App\Mail\SendMail`.
 */
class NotificationTemplateController extends Controller
{
    /**
     * Display configured notification message templates.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('notifications.templates.index');
    }
}
