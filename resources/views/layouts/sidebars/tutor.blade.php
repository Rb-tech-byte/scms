<div id="layoutSidenav_nav">
    @php

        $user_routes = resourceRoutes('users');
        $role_routes = resourceRoutes('roles');
        $permission_routes = resourceRoutes('permissions');
        $course_routes = resourceRoutes('courses');
        $venue_routes = resourceRoutes('venues');
        $branch_routes = resourceRoutes('branches');
        $applicant_routes = resourceRoutes('applicants');
        $payment_routes = resourceRoutes('payments');
        $invoice_routes = resourceRoutes('invoices');
        $invoice_route = resourceRoutes('invoices');

        $dashboard_routes = resourceRoutes('home');
        $system_routes = resourceRoutes('settings');
        $systemLogs_routes = resourceRoutes('system.logs');
        $course_assignments = resourceRoutes('course-assignments');

        $settings = \App\Models\Setting::first();
    @endphp
    <nav
        class="sidenav {{ !empty($setting) && $settings->allow_dark_sidebar == true ? 'sidenav-dark' : 'sidenav-light' }}   shadow-right">
        <div class="sidenav-menu">
            <div class="nav accordion" id="accordionSidenav">
                <div class="sidenav-menu-heading d-sm-none">Account</div>
                <a class="nav-link d-sm-none" href="#!">
                    <div class="nav-link-icon"><i data-feather="bell"></i></div>
                    Alerts
                    <span class="badge bg-warning-soft text-warning ms-auto">4 New!</span>
                </a>
                <a class="nav-link d-sm-none" href="#!">
                    <div class="nav-link-icon"><i data-feather="mail"></i></div>
                    Messages
                    <span class="badge bg-success-soft text-success ms-auto">2 New!</span>
                </a>
                <div class="mt-3"></div>
                <a class="nav-link {{ request()->route()->getName() == 'home'? 'active': '' }}"
                    href="{{ route('home') }}">
                    <div class="nav-link-icon"><i data-feather="grid"></i></div>
                    Dashboard
                </a>
                <div class="sidenav-menu-heading">ADMINISTRATION</div>
                {!! generateSidenavAccordion('applicants', 'users', 'Registration', [
                    ['name' => 'applicants.create', 'title' => 'New Registration'],
                    ['name' => 'applicants.index', 'title' => 'Manage'],
                ]) !!}

                {!! generateSidenavAccordion('enrollments', 'user-check', 'Enrollments', [
                    ['name' => 'enrollments.index', 'title' => 'Manage'],
                ]) !!}
                
                <div class="sidenav-menu-heading">SCHEDULES</div>
                {!! generateSidenavAccordion('course-assignments', 'book', 'Timetable', [
                    ['name' => 'course-assignments.index', 'title' => 'Personal'],
                    ['name' => 'course-assignment.branch', 'title' => 'Branch'],
                ]) !!}

                <div class="sidenav-menu-heading">CERTIFICATES</div>
                <a class="nav-link" href="{{ url('certificates') }}">
                    <div class="nav-link-icon"><i class="fas fa-award"></i></div>
                    Generate Certificates
                </a>
                <a class="nav-link" href="{{ url('certificates/verify') }}">
                    <div class="nav-link-icon"><i class="fas fa-qrcode"></i></div>
                    Verify Certificate
                </a>

                <div class="sidenav-menu-heading">ATTENDANCE</div>
                <a class="nav-link" href="{{ url('attendance') }}">
                    <div class="nav-link-icon"><i class="fas fa-user-check"></i></div>
                    Sessions & Attendance
                </a>
                <a class="nav-link" href="{{ url('attendance/export') }}">
                    <div class="nav-link-icon"><i class="fas fa-file-export"></i></div>
                    Attendance Export
                </a>
                <div class="my-4"></div>
            </div>
        </div>
    </nav>
</div>
