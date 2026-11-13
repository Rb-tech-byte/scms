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
        $systemLogs_routes = resourceRoutesCopy('system');
        $course_assignments = resourceRoutes('course-assignments');
        $perm_route =  resourceRoutesCopy('permission');

        $settings = \App\Models\Setting::first();
    @endphp
    <nav
        class="sidenav {{ !empty($setting) && $settings->allow_dark_sidebar == 1 ? 'sidenav-dark' : 'sidenav-light' }}   shadow-right">
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
                @if(auth()->user()->hasRole(['system-admin', 'tutor', 'branch-admin', 'training-coordinator', 'marketing-officer', 'director']))
                    @check('view-dashboard')
                    <a class="nav-link {{ request()->route()->getName() == 'home'? 'active': '' }}"
                        href="{{ route('home') }}">
                        <div class="nav-link-icon"><i data-feather="grid"></i></div>
                        Dashboard
                    </a>
                    @endcheck

                    @if(auth()->user()->hasRole(['director', 'system-admin', 'branch-admin', 'training-coordinator']))
                    <a class="nav-link {{ request()->routeIs('active.classes') ? 'active' : '' }}"
                        href="{{ route('active.classes') }}">
                        <div class="nav-link-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        Active Classes
                    </a>
                    @endif

                    @check('access-administration')
                    <div class="sidenav-menu-heading">ADMINISTRATION</div>
                    @endcheck


                    @checkBetween('register-applicants','list-applicants')
                    {!! generateSidenavAccordion('applicants', 'users', 'Registration', [
                        ['name' => 'applicants.create', 'title' => 'New Registration','permission'=>'register-applicants'],
                        ['name' => 'applicants.index', 'title' => 'Manage','permission'=>'list-applicants'],
                    ]) !!}
                    @endcheckBetween


                    @check('list-enrollments')
                    {!! generateSidenavAccordion('enrollments', 'user-check', 'Enrollments', [
                        ['name' => 'enrollments.index', 'title' => 'Manage','permission'=>'list-enrollments'],
                    ]) !!}
                    @endcheck


                    @checkBetween('list-payments','list-invoices')
                    <div class="sidenav-menu-heading">ACCOUNTING</div>
                    @endcheckBetween


                    @check('list-invoices')
                    {!! generateSidenavAccordion('invoices', 'file-text', 'Invoices', [
                        ['name' => 'invoices.index', 'title' => 'Manage','permission'=>'list-invoices'],
                        ['name' => 'invoice.partial', 'title' => 'Partial Invoices','permission'=>'list-invoices'],
                        ['name' => 'invoice.paid', 'title' => 'Paid Invoices','permission'=>'list-invoices'],
                    ]) !!}
                    @endcheck


                    @checkBetween('list-payments','add-payments')
                    {!! generateSidenavAccordion('payments', 'dollar-sign', 'Payments', [
                        ['name' => 'payments.create', 'title' => 'Add New','permission'=>'list-payments'],
                        ['name' => 'payments.index', 'title' => 'Manage','permission'=>'add-payments'],
                    ]) !!}
                    @endcheckBetween

                    @check('list-invoices')
                    <div class="sidenav-menu-heading">SCHEDULES & REMINDERS</div>
                    <a class="nav-link" href="{{ url('payment-schedules') }}">
                        <div class="nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                        Payment Schedules
                    </a>
                    <a class="nav-link" href="{{ url('payment-reminders') }}">
                        <div class="nav-link-icon"><i class="fas fa-bell"></i></div>
                        Due Reminders
                    </a>
                    @endcheck

                    @checkBetween('list-tutors','list-courseassignments','view-courseassignments')
                    <div class="sidenav-menu-heading">ACADEMIC & SCHEDULES</div>
                    @endcheckBetween


                    @checkBetween('list-tutors','add-tutors')
                    {!! generateSidenavAccordion('tutors', 'user-plus', 'Tutors', [
                        ['name' => 'tutors.index', 'title' => 'Manage','permission'=>'list-tutors'],
                        ['name' => 'tutors.create', 'title' => 'Add New','permission'=>'add-tutors'],
                    ]) !!}
                    @endcheckBetween


                    @check('list-courseassignments')
                    {!! generateSidenavAccordion('course-assignments', 'user-check', 'Course-Assignments', [
                        ['name' => 'course-assignments.index', 'title' => 'Manage','permission'=>'list-courseassignments'],
                    ]) !!}
                    @endcheck

                    @checkBetween('issue-certificate','clear-applicant','clear-payments')
                    <div class="sidenav-menu-heading">CERTIFICATES</div>
                    <a class="nav-link" href="{{ url('certificates') }}">
                        <div class="nav-link-icon"><i class="fas fa-award"></i></div>
                        Generate Certificates
                    </a>
                    <a class="nav-link" href="{{ url('certificates/verify') }}">
                        <div class="nav-link-icon"><i class="fas fa-qrcode"></i></div>
                        Verify Certificate
                    </a>
                    @endcheckBetween

                    @check('list-courseassignments')
                    <div class="sidenav-menu-heading">ATTENDANCE</div>
                    <a class="nav-link" href="{{ url('attendance') }}">
                        <div class="nav-link-icon"><i class="fas fa-user-check"></i></div>
                        Sessions & Attendance
                    </a>
                    <a class="nav-link" href="{{ url('attendance/export') }}">
                        <div class="nav-link-icon"><i class="fas fa-file-export"></i></div>
                        Attendance Export
                    </a>
                    @endcheck

                    @if(auth()->user()->hasRole('tutor') )
                    <div class="sidenav-menu-heading">SCHEDULES</div>
                    {!! generateSidenavAccordion('course-assignments', 'book', 'Timetable', [
                        ['name' => 'course-assignment.branch', 'title' => 'Branch'],
                    ]) !!}
                    @endif

                    @check('List settings')
                    <div class="sidenav-menu-heading">Settings</div>


                    @check('List branches')
                    <a class="nav-link {{ $branch_routes ? 'active' : '' }}" href="{{ route('branches.index') }}">
                        <div class="nav-link-icon"><i data-feather="git-merge"></i></div>
                        Branches
                    </a>
                    @endcheck
                    @check('List venues')
                    <a class="nav-link {{ $venue_routes ? 'active' : '' }}" href="{{ route('venues.index') }}">
                        <div class="nav-link-icon"><i data-feather="home"></i></div>
                        Venues
                    </a>
                    @endcheck

                    @check('List courses')
                    <a class="nav-link  {{ $course_routes ? 'active' : '' }}" href="{{ route('courses.index') }}">
                        <div class="nav-link-icon"><i data-feather="book-open"></i></div>
                        Courses
                    </a>
                    @endcheck
                    @check('Update settings')
                    <a class="nav-link  {{ $system_routes ? 'active' : '' }}" href="{{ route('settings.create') }}">
                        <div class="nav-link-icon"><i class="fa-solid fa-gear"></i></div>
                        Settings
                    </a>
                    @endcheck

                    @check('view-notifications')
                    <div class="sidenav-menu-heading">NOTIFICATIONS</div>
                    <a class="nav-link" href="{{ url('notifications') }}">
                        <div class="nav-link-icon"><i class="fas fa-bell"></i></div>
                        Notification Center
                    </a>
                    <a class="nav-link" href="{{ url('notifications/templates') }}">
                        <div class="nav-link-icon"><i class="fas fa-envelope-open-text"></i></div>
                        Templates
                    </a>
                    @endcheck
                    @check('Access management')
                    <a class="nav-link {{ $role_routes || $user_routes ||$permission_routes || $perm_route ? 'active' : 'collapsed' }}"
                        href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#accessComponents"
                        aria-expanded="false" aria-controls="accessComponents">
                        <div class="nav-link-icon"><i class="fa-solid fa-user-lock"></i></div>
                        Access Management
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ $role_routes || $user_routes || $systemLogs_routes || $permission_routes|| $perm_route ? 'show' : '' }}"
                        id="accessComponents" data-bs-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            @check('Manage staffs')
                            <a class="nav-link  {{ $user_routes ? 'active' : '' }}"
                                href="{{ route('users.index') }}">Staffs</a>
                                @endcheck

                                @check('List roles')
                            <a class="nav-link {{ $role_routes ? 'active' : '' }}"
                                href="{{ route('roles.index') }}">Role</a>
                                @endcheck

                                @check('List Permissions')
                                <a class="nav-link {{ $permission_routes ? 'active' : '' }}"
                                    href="{{ route('permissions.index') }}">Permisions</a>
                                @endcheck

                                @check('List Permissions')
                                <a class="nav-link {{ $perm_route ? 'active' : '' }}"
                                href="{{ route('permission.manage') }}">Manage Permisions</a>
                                @endcheck

                                @check('View activity logs')
                            <a class="nav-link {{ $systemLogs_routes ? 'active' : '' }}"
                                href="{{ route('system.logs') }}">System logs</a>
                                @endcheck

                        </nav>
                    </div>
                    @endcheck
                    @endcheck
                    @check('View all reports')
                    <div class="sidenav-menu-heading">REPORTS</div>
                {!! generateSidenavAccordion('reports', 'file', 'Reports', [
                        ['name' => 'applicant.report', 'title' => 'View Applicant Report','permission'=>'view-all-reports'],
                        ['name' => 'reports.center_courses', 'title' => 'Center Course Report','permission'=>'view-all-reports'],
                    ]) !!}
                    @endcheck

                    
                @endif

                @if(auth()->user()->hasRole('student'))
                <div class="sidenav-menu-heading">STUDENT PORTAL</div>
                <a class="nav-link {{ request()->routeIs('student.portal') ? 'active' : '' }}" href="{{ route('student.portal') }}">
                    <div class="nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                    Enroll, Invoices & Receipts
                </a>

                <div class="sidenav-menu-heading">PAYMENTS</div>
                {!! generateSidenavAccordion('invoices', 'file-text', 'Invoices', [
                    ['name' => 'invoices.index', 'title' => 'View'],
                ]) !!}


                <div class="sidenav-menu-heading">SCHEDULES</div>
                {!! generateSidenavAccordion('course-assignments', 'book', 'Timetable', [
                    ['name' => 'course-assignment.branch', 'title' => 'View'],
                ]) !!}
                @endif

                <div class="sidenav-menu-heading">SUPPORT</div>
                <a class="nav-link {{ request()->routeIs('help.index') ? 'active' : '' }}" href="{{ route('help.index') }}">
                    <div class="nav-link-icon"><i class="fas fa-question-circle"></i></div>
                    Online Help
                </a>
                <div class="my-4"></div>
            </div>
        </div>
    </nav>
</div>
