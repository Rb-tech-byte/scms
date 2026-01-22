<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\CourseAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseAssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'active' => $this->faker->boolean(),
            'branch_id' => \App\Models\Branch::factory(),
            'venue_id' => \App\Models\Venue::factory(),
            'course_id' => \App\Models\Course::factory(),
            'tutor_id' => \App\Models\Tutor::factory(),
        ];
    }
}
