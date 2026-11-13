<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Password123456789@');
        $branchId = Branch::value('id') ?? 1;
        $countryId = Country::value('id') ?? 1;

        $systemAdminRoleId = Role::where('name', 'system-admin')->value('id') ?? 1;
        $branchAdminRoleId = Role::where('name', 'branch-admin')->value('id') ?? 2;
        $studentRoleId = Role::where('name', 'student')->value('id') ?? 4;

        // 1. Administrator (system-admin)
        User::updateOrCreate(
            ['email' => 'admin@scms.out.co.tz'],
            [
                'name' => 'Super Admin',
                'password' => $password,
                'user_type' => 'staff',
                'davinci_role_id' => $systemAdminRoleId,
                'force_password_reset' => false,
            ]
        );

        // 2. Branch Manager (branch-admin)
        User::updateOrCreate(
            ['email' => 'branch@scms.out.co.tz'],
            [
                'name' => 'Dev Branch Manager',
                'password' => $password,
                'user_type' => 'staff',
                'davinci_role_id' => $branchAdminRoleId,
                'branch_id' => $branchId,
                'force_password_reset' => false,
            ]
        );

        // 3. Student (student + linked Applicant profile for StudentDashboard)
        $student = User::updateOrCreate(
            ['email' => 'student@scms.out.co.tz'],
            [
                'name' => 'Dev Student',
                'password' => $password,
                'user_type' => 'student',
                'davinci_role_id' => $studentRoleId,
                'branch_id' => $branchId,
                'force_password_reset' => false,
            ]
        );

        Applicant::updateOrCreate(
            ['user_id' => $student->id],
            [
                'full_name' => 'Dev Student',
                'country_id' => $countryId,
                'mobile' => '0700000000',
                'email' => $student->email,
                'education_level' => '5',
                'branch_id' => $branchId,
            ]
        );
    }
}
