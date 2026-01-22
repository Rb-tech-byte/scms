<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseAssignment;

class CourseAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseAssignment::factory()
            ->count(0)
            ->create();
    }
}
