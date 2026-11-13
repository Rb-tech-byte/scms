<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\NotificationTemplate;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds SRS-defined roles, permissions, default notification templates, and local demo users.
 */
class SrsRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'system-admin',
            'branch-admin',
            'training-coordinator',
            'marketing-officer',
            'director',
            'tutor',
            'student',
        ];

        $roleIds = [];
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $roleIds[$roleName] = $role->id;
        }

        $permissions = [
            ['name' => 'view-dashboard', 'category' => 'administrative'],
            ['name' => 'access-administration', 'category' => 'administrative'],
            ['name' => 'register-applicants', 'category' => 'administrative'],
            ['name' => 'list-applicants', 'category' => 'administrative'],
            ['name' => 'list-enrollments', 'category' => 'courses'],
            ['name' => 'add-enrollments', 'category' => 'courses'],
            ['name' => 'view-enrollments', 'category' => 'courses'],
            ['name' => 'list-invoices', 'category' => 'accounting'],
            ['name' => 'list-payments', 'category' => 'accounting'],
            ['name' => 'add-payments', 'category' => 'accounting'],
            ['name' => 'list-tutors', 'category' => 'staffs'],
            ['name' => 'add-tutors', 'category' => 'staffs'],
            ['name' => 'update-tutors', 'category' => 'staffs'],
            ['name' => 'list-courseassignments', 'category' => 'courses'],
            ['name' => 'view-courseassignments', 'category' => 'courses'],
            ['name' => 'add-courseassignments', 'category' => 'courses'],
            ['name' => 'update-courseassignments', 'category' => 'courses'],
            ['name' => 'list-courses', 'category' => 'courses'],
            ['name' => 'add-courses', 'category' => 'courses'],
            ['name' => 'view-courses', 'category' => 'courses'],
            ['name' => 'update-courses', 'category' => 'courses'],
            ['name' => 'list-branches', 'category' => 'administrative'],
            ['name' => 'add-branches', 'category' => 'administrative'],
            ['name' => 'list-venues', 'category' => 'administrative'],
            ['name' => 'add-venues', 'category' => 'administrative'],
            ['name' => 'list-settings', 'category' => 'administrative'],
            ['name' => 'view-notifications', 'category' => 'administrative'],
            ['name' => 'manage-notification-templates', 'category' => 'administrative'],
            ['name' => 'view-all-reports', 'category' => 'administrative'],
            ['name' => 'view-center-reports', 'category' => 'administrative'],
            ['name' => 'issue-certificate', 'category' => 'certificate'],
            ['name' => 'view-ready-certificates', 'category' => 'certificate'],
            ['name' => 'send-certificate-alerts', 'category' => 'certificate'],
            ['name' => 'view-active-classes', 'category' => 'courses'],
            ['name' => 'manage-attendance', 'category' => 'courses'],
            ['name' => 'print-overdue-alerts', 'category' => 'accounting'],
            ['name' => 'manage-staffs', 'category' => 'staffs'],
            ['name' => 'block-users', 'category' => 'staffs'],
            ['name' => 'unblock-users', 'category' => 'staffs'],
            ['name' => 'view-activity-logs', 'category' => 'administrative'],
        ];

        $permIds = [];
        foreach ($permissions as $permData) {
            $perm = Permission::firstOrCreate(
                ['name' => $permData['name']],
                ['category' => $permData['category']]
            );
            $permIds[$permData['name']] = $perm->id;
        }

        $rolePermissionsMap = [
            'training-coordinator' => [
                'view-dashboard', 'access-administration', 'register-applicants', 'list-applicants',
                'list-enrollments', 'view-enrollments', 'list-invoices', 'list-payments',
                'list-tutors', 'add-tutors', 'update-tutors', 'list-courseassignments',
                'view-courseassignments', 'add-courseassignments', 'update-courseassignments',
                'list-courses', 'add-courses', 'view-courses', 'update-courses',
                'list-branches', 'add-branches', 'list-venues', 'add-venues', 'list-settings',
                'view-notifications', 'view-all-reports', 'view-center-reports',
                'issue-certificate', 'view-ready-certificates', 'send-certificate-alerts',
                'view-active-classes', 'manage-attendance', 'print-overdue-alerts',
            ],
            'branch-admin' => [
                'view-center-reports', 'issue-certificate', 'view-ready-certificates',
                'send-certificate-alerts', 'view-active-classes', 'manage-attendance', 'print-overdue-alerts',
            ],
            'marketing-officer' => [
                'view-dashboard', 'access-administration', 'list-applicants', 'list-enrollments',
                'view-enrollments', 'list-invoices', 'list-payments', 'list-courses',
                'add-courses', 'view-courses', 'update-courses', 'list-settings',
                'view-notifications', 'view-all-reports', 'view-center-reports', 'print-overdue-alerts',
            ],
            'director' => [
                'view-dashboard', 'view-active-classes', 'view-ready-certificates',
                'view-all-reports', 'view-center-reports', 'list-courses', 'view-courses',
                'list-branches', 'list-settings', 'view-notifications',
            ],
            'tutor' => [
                'manage-attendance', 'view-ready-certificates', 'view-active-classes',
            ],
        ];

        foreach ($rolePermissionsMap as $roleName => $perms) {
            $roleId = $roleIds[$roleName] ?? null;
            if (!$roleId) {
                continue;
            }
            foreach ($perms as $permName) {
                $permId = $permIds[$permName] ?? null;
                if (!$permId) {
                    continue;
                }
                RolePermission::updateOrCreate(
                    ['role_id' => $roleId, 'permission_id' => $permId],
                    ['granted' => 'yes']
                );
            }
        }

        // Seed default SRS notification templates
        $templates = [
            [
                'key' => 'payment_overdue_alert',
                'name' => 'Payment Overdue Alert',
                'channel' => 'both',
                'subject' => 'Overdue Tuition Payment Notice - {course_title}',
                'body' => 'Dear {student_name}, your installment for {course_title} (Invoice #{invoice_number}, Control #{control_number}) is overdue. Outstanding balance: {balance} TZS due on {due_date}. Please complete payment at your earliest convenience.',
                'is_active' => true,
            ],
            [
                'key' => 'certificates_ready_tutor',
                'name' => 'Certificates Ready Instructor Alert',
                'channel' => 'email',
                'subject' => 'Course Certificates Ready - {course_title} ({branch_name})',
                'body' => 'Dear {tutor_name}, certificates for {course_title} at {branch_name} ({ready_count} eligible student(s)) are now ready for verification and collection.',
                'is_active' => true,
            ],
            [
                'key' => 'account_lockout_alert',
                'name' => 'Account Security Lockout Alert',
                'channel' => 'email',
                'subject' => 'Security Alert: Account Temporarily Locked',
                'body' => 'Security notice for {user_name} ({email}): your account experienced repeated failed login attempts and has been temporarily locked/flagged for review.',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $tpl) {
            NotificationTemplate::firstOrCreate(['key' => $tpl['key']], $tpl);
        }

        // Seed demo staff accounts for local/dev evaluation when missing
        if (app()->environment('local', 'testing')) {
            $password = Hash::make('Password123456789@');
            $branchId = Branch::value('id') ?? 1;

            User::updateOrCreate(
                ['email' => 'director@scms.out.co.tz'],
                [
                    'name' => 'Dev Director',
                    'password' => $password,
                    'user_type' => 'staff',
                    'davinci_role_id' => $roleIds['director'],
                    'force_password_reset' => false,
                    'password_changed_at' => now(),
                ]
            );

            User::updateOrCreate(
                ['email' => 'marketing@scms.out.co.tz'],
                [
                    'name' => 'Dev Marketing Officer',
                    'password' => $password,
                    'user_type' => 'staff',
                    'davinci_role_id' => $roleIds['marketing-officer'],
                    'force_password_reset' => false,
                    'password_changed_at' => now(),
                ]
            );

            $tutorUser = User::updateOrCreate(
                ['email' => 'tutor@scms.out.co.tz'],
                [
                    'name' => 'Dev Instructor',
                    'password' => $password,
                    'user_type' => 'staff',
                    'davinci_role_id' => $roleIds['tutor'],
                    'branch_id' => $branchId,
                    'force_password_reset' => false,
                    'password_changed_at' => now(),
                ]
            );

            Tutor::firstOrCreate(
                ['user_id' => $tutorUser->id],
                [
                    'name' => $tutorUser->name,
                    'email' => $tutorUser->email,
                    'phone' => '0711000001',
                    'address' => 'Dar es Salaam',
                    'branch_id' => $branchId,
                    'active' => 1,
                ]
            );
        }
    }
}
