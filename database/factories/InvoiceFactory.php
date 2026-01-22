<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'balance' => $this->faker->randomNumber(2), // 'balance' => '0
            'paid_amount' => $this->faker->randomNumber(2),
            'description' => $this->faker->sentence(15),
            'date' => $this->faker->date(),
            'status' => '0',
            'applicant_id' => \App\Models\Applicant::factory(),
            'enrollment_id' => \App\Models\Enrollment::factory(),
        ];
    }
}
