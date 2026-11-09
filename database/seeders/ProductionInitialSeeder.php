<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\Setting;
use App\Models\Country;
use App\Models\Branch;

/**
 * Class ProductionInitialSeeder
 *
 * Idempotent seeder designed specifically for automated CI/CD and deployment pipelines.
 * Guarantees that essential system structures, reference tables, permissions,
 * and compliant administrative credentials exist without duplicating records
 * or disrupting existing production transactional data.
 *
 * @author Renatus Bernardo <bernadorenatus32@gmail.com>
 */
class ProductionInitialSeeder extends Seeder
{
    /**
     * Run the deployment database seed.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command?->info('Seeding core roles...');
        $roles = [
            'system-admin',
            'branch-admin',
            'tutor',
            'student',
        ];

        $roleModels = [];
        foreach ($roles as $roleName) {
            $roleModels[$roleName] = Role::firstOrCreate(['name' => $roleName]);
        }

        $this->command?->info('Seeding permissions catalog...');
        $permissions = [
            // Administrative permissions
            ['name' => 'list-applicants', 'category' => 'administrative'],
            ['name' => 'view-applicants', 'category' => 'administrative'],
            ['name' => 'register-applicants', 'category' => 'administrative'],
            ['name' => 'update-applicants', 'category' => 'administrative'],
            ['name' => 'delete-applicants', 'category' => 'administrative'],

            ['name' => 'list-branches', 'category' => 'administrative'],
            ['name' => 'view-branches', 'category' => 'administrative'],
            ['name' => 'register-branches', 'category' => 'administrative'],
            ['name' => 'update-branches', 'category' => 'administrative'],
            ['name' => 'delete-branches', 'category' => 'administrative'],

            ['name' => 'list-countries', 'category' => 'administrative'],
            ['name' => 'view-countries', 'category' => 'administrative'],
            ['name' => 'add-countries', 'category' => 'administrative'],
            ['name' => 'update-countries', 'category' => 'administrative'],
            ['name' => 'delete-countries', 'category' => 'administrative'],

            ['name' => 'list-settings', 'category' => 'administrative'],
            ['name' => 'view-settings', 'category' => 'administrative'],
            ['name' => 'add-settings', 'category' => 'administrative'],
            ['name' => 'update-settings', 'category' => 'administrative'],
            ['name' => 'delete-settings', 'category' => 'administrative'],

            ['name' => 'list-tutors', 'category' => 'administrative'],
            ['name' => 'view-tutors', 'category' => 'administrative'],
            ['name' => 'add-tutors', 'category' => 'administrative'],
            ['name' => 'update-tutors', 'category' => 'administrative'],
            ['name' => 'delete-tutors', 'category' => 'administrative'],

            ['name' => 'accept-course-discount', 'category' => 'administrative'],

            ['name' => 'list-roles', 'category' => 'administrative'],
            ['name' => 'view-roles', 'category' => 'administrative'],
            ['name' => 'add-roles', 'category' => 'administrative'],
            ['name' => 'update-roles', 'category' => 'administrative'],
            ['name' => 'delete-roles', 'category' => 'administrative'],

            ['name' => 'list-permissions', 'category' => 'administrative'],
            ['name' => 'view-permissions', 'category' => 'administrative'],
            ['name' => 'add-permissions', 'category' => 'administrative'],
            ['name' => 'update-permissions', 'category' => 'administrative'],
            ['name' => 'delete-permissions', 'category' => 'administrative'],

            ['name' => 'clear-applicant', 'category' => 'certificate'],
            ['name' => 'issue-certificate', 'category' => 'certificate'],
            ['name' => 'clear-payments', 'category' => 'certificate'],

            // Accounting permissions
            ['name' => 'list-invoices', 'category' => 'accounting'],
            ['name' => 'view-invoices', 'category' => 'accounting'],
            ['name' => 'add-invoices', 'category' => 'accounting'],
            ['name' => 'update-invoices', 'category' => 'accounting'],
            ['name' => 'delete-invoices', 'category' => 'accounting'],

            ['name' => 'list-payments', 'category' => 'accounting'],
            ['name' => 'view-payments', 'category' => 'accounting'],
            ['name' => 'add-payments', 'category' => 'accounting'],
            ['name' => 'update-payments', 'category' => 'accounting'],
            ['name' => 'delete-payments', 'category' => 'accounting'],

            // Courses permissions
            ['name' => 'list-courses', 'category' => 'courses'],
            ['name' => 'view-courses', 'category' => 'courses'],
            ['name' => 'add-courses', 'category' => 'courses'],
            ['name' => 'update-courses', 'category' => 'courses'],
            ['name' => 'delete-courses', 'category' => 'courses'],
            ['name' => 'add-course-discount', 'category' => 'courses'],

            ['name' => 'list-enrollments', 'category' => 'courses'],
            ['name' => 'view-enrollments', 'category' => 'courses'],
            ['name' => 'add-enrollments', 'category' => 'courses'],
            ['name' => 'update-enrollments', 'category' => 'courses'],
            ['name' => 'delete-enrollments', 'category' => 'courses'],

            // Staff & Facilities permissions
            ['name' => 'list-courseassignments', 'category' => 'staffs'],
            ['name' => 'view-courseassignments', 'category' => 'staffs'],
            ['name' => 'add-courseassignments', 'category' => 'staffs'],
            ['name' => 'update-courseassignments', 'category' => 'staffs'],
            ['name' => 'delete-courseassignments', 'category' => 'staffs'],

            ['name' => 'list-venues', 'category' => 'staffs'],
            ['name' => 'view-venues', 'category' => 'staffs'],
            ['name' => 'add-venues', 'category' => 'staffs'],
            ['name' => 'update-venues', 'category' => 'staffs'],
            ['name' => 'delete-venues', 'category' => 'staffs'],

            ['name' => 'list-notifications', 'category' => 'staffs'],
            ['name' => 'view-notifications', 'category' => 'staffs'],
            ['name' => 'add-notifications', 'category' => 'staffs'],
            ['name' => 'delete-notifications', 'category' => 'staffs'],

            ['name' => 'view-dashboard', 'category' => 'administrative'],
            ['name' => 'disable-student', 'category' => 'administrative'],
            ['name' => 'disable-staff', 'category' => 'administrative'],
            ['name' => 'manage-staffs', 'category' => 'administrative'],
            ['name' => 'access-management', 'category' => 'administrative'],
            ['name' => 'view-activity-logs', 'category' => 'administrative'],
            ['name' => 'view-all-reports', 'category' => 'staffs'],
            ['name' => 'view-branch-reports', 'category' => 'staffs'],
        ];

        foreach ($permissions as $perm) {
            $permissionModel = Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['category' => $perm['category']]
            );

            // Automatically grant all permissions to system-admin role
            if (isset($roleModels['system-admin'])) {
                RolePermission::firstOrCreate([
                    'role_id' => $roleModels['system-admin']->id,
                    'permission_id' => $permissionModel->id,
                ]);
            }
        }

        $this->command?->info('Seeding system settings...');
        Setting::firstOrCreate(
            ['id' => 1],
            [
                'system_name' => 'Institute of Education and Management Technologies (IEMT)',
                'system_logo' => 'logo.png',
                'site_description' => 'Short Course Management System - The Open University of Tanzania',
                'site_contact_email' => 'ict.training@out.ac.tz',
                'site_contact_number' => '+255799888234',
                'enable_upload_receipt' => 1,
            ]
        );

        $this->command?->info('Checking countries catalog...');
        if (Country::count() === 0) {
            $this->call(CountrySeeder::class);
        }

        $this->command?->info('Checking branch centres...');
        if (Branch::count() === 0) {
            $this->call(BranchSeeder::class);
        }

        $this->command?->info('Verifying compliant baseline users...');
        $adminRole = $roleModels['system-admin'] ?? Role::where('name', 'system-admin')->first();
        $tutorRole = $roleModels['tutor'] ?? Role::where('name', 'tutor')->first();
        $studentRole = $roleModels['student'] ?? Role::where('name', 'student')->first();

        // 1. System Admin
        $admin = User::firstOrNew(['email' => 'admin@scms.out.ac.tz']);
        if (!$admin->exists) {
            $admin->name = 'System Administrator';
            $admin->phone = '255799888234';
            $admin->user_type = 'staff';
            $admin->davinci_role_id = $adminRole?->id ?? 1;
            $admin->force_password_reset = 0;
            $admin->password = Hash::make('Scms@Admin2026!#');
        } else {
            $admin->davinci_role_id = $adminRole?->id ?? 1;
            $admin->force_password_reset = 0;
            if (empty($admin->password) || !Hash::check('Scms@Admin2026!#', $admin->password)) {
                $admin->password = Hash::make('Scms@Admin2026!#');
            }
        }
        $admin->save();

        // Grant all permissions to the admin user
        $allPermIds = Permission::pluck('id');
        foreach ($allPermIds as $pId) {
            UserPermission::firstOrCreate([
                'user_id' => $admin->id,
                'permission_id' => $pId,
            ], [
                'granted' => 'yes',
            ]);
        }

        // 2. Staff / Tutor
        $tutor = User::firstOrCreate(
            ['email' => 'info@wolinet.com'],
            [
                'name' => 'Senior Instructor / Tutor',
                'phone' => '255799888233',
                'user_type' => 'staff',
                'password' => Hash::make('Scms@Admin2026!#'),
                'davinci_role_id' => $tutorRole?->id ?? 3,
                'force_password_reset' => 0,
            ]
        );

        // 3. Test Student
        $student = User::firstOrCreate(
            ['email' => 'bernadorenatus32@gmail.com'],
            [
                'name' => 'Renatus Bernardo',
                'phone' => '255799888232',
                'user_type' => 'student',
                'password' => Hash::make('Scms@Admin2026!#'),
                'davinci_role_id' => $studentRole?->id ?? 4,
                'force_password_reset' => 0,
            ]
        );

        $this->command?->info('Production initial seeding completed successfully.');
    }
}
