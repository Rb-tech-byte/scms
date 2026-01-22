<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'system_name' => $this->faker->text(255),
            'allow_dark_sidebar' => $this->faker->boolean(),
            'allow_message' => $this->faker->boolean(),
            'allow_email' => $this->faker->boolean(),
            'enable_upload_receipt' => $this->faker->boolean(),
            'mail_mailer' => $this->faker->text(255),
            'mail_host' => $this->faker->text(255),
            'mail_port' => $this->faker->text(255),
            'mail_username' => $this->faker->text(255),
            'mail_password' => $this->faker->text(255),
            'mail_encryption' => $this->faker->text(255),
            'mail_from_address' => $this->faker->text(255),
            'site_description' => $this->faker->text(),
            'site_contact_email' => $this->faker->text(255),
            'site_contact_number' => $this->faker->text(255),
        ];
    }
}
