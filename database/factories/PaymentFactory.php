<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_status' => $this->faker->boolean(),
            'amount' => $this->faker->randomNumber(2),
            'date' => $this->faker->dateTime(),
            'invoice_id' => \App\Models\Invoice::factory(),
            'applicant_id' => \App\Models\Applicant::factory(),
            'course_id' => \App\Models\Course::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
