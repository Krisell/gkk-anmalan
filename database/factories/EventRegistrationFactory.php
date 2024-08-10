<?php

namespace Database\Factories;

use App\EventRegistration;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventRegistrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => true,
            'user_id' => User::factory(),
        ];
    }
}
