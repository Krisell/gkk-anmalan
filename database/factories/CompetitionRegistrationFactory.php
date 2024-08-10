<?php

namespace Database\Factories;

use App\Competition;
use App\CompetitionRegistration;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionRegistrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompetitionRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'competition_id' => Competition::factory(),
            'user_id' => User::factory(),
            'licence_number' => '010101ab',
            'gender' => 'Män',
            'weight_class' => '74',
            'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
            'status' => true,
        ];
    }
}
