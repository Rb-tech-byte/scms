<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

use App\Models\User;

class PermissionSeeder2 extends Seeder
{
    public function run(): void
    {

        $roles = [
            ["name"=> "system-admin"],
            ["name"=> "branch-admin"],
            ["name"=> "tutor"],
            ["name"=> "student"],
        ];

        Role::insert($roles);
        
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

            // Students permissions
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

            // Staffs permissions
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

        Permission::insert($permissions);
        User::where('email','admin@scms.out.ac.tz')->update(["davinci_role_id"=>1]);
    }
}
