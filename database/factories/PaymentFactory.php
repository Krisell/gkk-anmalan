<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => 'MEMBERSHIP',
            'year' => $this->faker->year,
            'sek_amount' => $this->faker->numberBetween(100, 1000),
            'fortnox_invoice_created_at' => $this->faker->dateTimeBetween('-60 days', '-45 days'),
            'fortnox_invoice_emailed_at' => $this->faker->dateTimeBetween('-45 days', '-1 days'),
        ];
    }
}
