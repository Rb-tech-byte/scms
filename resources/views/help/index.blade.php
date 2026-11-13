@extends('layouts.app')
@section('title', 'Online Help & User Guide')
@section('content')
<div class="container-xl px-4 mt-4">
    <h1 class="h3 mb-3"><i class="fas fa-question-circle text-primary me-2"></i>SCMIS Online Help Center</h1>
    <p class="text-muted">Signed in as <strong>{{ $user->name }}</strong> (<span class="badge bg-primary">{{ $roleName }}</span>).</p>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold text-primary">Student Workflows (R1)</div>
                <div class="card-body small">
                    <ul>
                        <li><strong>Enroll / Unenroll:</strong> Open <em>Student Portal</em>, choose course, teaching method, venue, session, and start date. Unenrollment is available for unpaid enrollments.</li>
                        <li><strong>Invoices & Control Number:</strong> Invoices generate automatically upon enrollment; click <em>Control #</em> to generate your payment control number.</li>
                        <li><strong>Payments & Receipts:</strong> Click <em>Pay / Upload Receipt</em> to upload PDF/JPG/PNG proof and view or print official receipts.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold text-primary">Training Coordinator, Instructor & Director (R2, R5, R6)</div>
                <div class="card-body small">
                    <ul>
                        <li><strong>Courses & Teaching Methods:</strong> Configure <em>Face to Face</em>, <em>Online</em>, or <em>Both</em> under Courses.</li>
                        <li><strong>Center Reports:</strong> Use <em>Center Course Report</em> for Weekly, Monthly, or Quarterly regional summaries showing full and partial payments.</li>
                        <li><strong>Certificates & Alerts:</strong> Track eligible/ready certificates, notify course instructors via email/alerts, verify QR serials publicly, and mark class attendance rosters.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header fw-bold text-primary">Account & Session Security Policies (SRS Section 5.1)</div>
                <div class="card-body small">
                    <ul class="mb-0">
                        <li><strong>Password Complexity & Expiry:</strong> Passwords require at least 8 characters with upper/lower case letters, numbers, and symbols, and expire every 3 months.</li>
                        <li><strong>Lockout Protection:</strong> 4 consecutive failed sign-in attempts pause login for 5 minutes; 2 lockout events block the account until unblocked by a Systems Administrator.</li>
                        <li><strong>Session Integrity:</strong> Idle sessions expire after 5 minutes of inactivity, and only one active session per account is permitted at a time.</li>
                        <li><strong>Form Draft Recovery:</strong> Unsaved non-password form data is automatically preserved in your browser for 15 minutes in case of accidental interruption.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

