<?php

namespace Database\Factories;

use App\Models\Enrollment;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Enrollment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'session' => 'morning',
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'fee' => $this->faker->randomNumber(2),
            'duration' => $this->faker->text(5),
            'applicant_id' => \App\Models\Applicant::factory(),
            'course_id' => \App\Models\Course::factory(),
            'venue_id' => \App\Models\Venue::factory(),
        ];
    }
}