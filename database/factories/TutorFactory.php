<?php

namespace Database\Factories;

use App\Models\Tutor;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TutorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tutor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'active' => $this->faker->boolean(),
            'branch_id' => \App\Models\Branch::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
