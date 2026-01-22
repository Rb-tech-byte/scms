<?php

namespace Database\Factories;

use App\Models\Applicant;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Applicant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'home_address' => $this->faker->text(5),
            'office_of_work' => $this->faker->text(5),
            'occupation' => $this->faker->text(5),
            'mobile' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'name_of_sponsor' => $this->faker->text(5),
            'phone_2' => $this->faker->text(5),
            'education_level' => $this->faker->text(5),
            'country_id' => 1,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}