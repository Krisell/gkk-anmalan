<?php

namespace Database\Factories;

use App\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Competition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => now()->addDays(random_int(10, 30)),
            'name' => $this->faker->name,
            'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        ];
    }
}
