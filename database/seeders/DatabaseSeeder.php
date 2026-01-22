<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Adding an admin user
        $user = \App\Models\User::factory()
            ->count(1)
            ->create([
                'name' => 'Admin Open University',
                'email' => 'admin@admin.com',
                'user_type' => 'staff',
                'phone' => '0123456789',
                'password' => Hash::make('admin'),
            ]);
        $this->call(PermissionSeeder2::class);
        $this->call(CountrySeeder::class);
        $this->call(ApplicantSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(CourseAssignmentSeeder::class);
        $this->call(EnrollmentSeeder::class);
        $this->call(InvoiceSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(NotificationSeeder::class);
        $this->call(PaymentSeeder::class);
        $this->call(TutorSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(VenueSeeder::class);
    }
}
