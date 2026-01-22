<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //mostly course are ict based courses
        $courseData = [
            [
                'title' => 'Basic computer Application',
                'fee' => 350000,
                'duration' => 4,
            ],
            [
                'title' => 'Video Production and Moving Graphics Design',
                'fee' => 390000,
                'duration' => 6,
            ],
        ];

        foreach ($courseData as $course) {
            Course::create($course);
        }
    }
}
