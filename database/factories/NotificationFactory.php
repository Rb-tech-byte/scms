<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'applicant_id' => $this->faker->randomNumber(),
            'user_id' => $this->faker->randomNumber(),
            'message' => $this->faker->sentence(1),
        ];
    }
}
