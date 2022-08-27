<?php

namespace Database\Factories;

use App\Result;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Result::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'gender' => 'M',
            'competition_date' => '2021-03-02',
            'weight_class' => '74',
            'event' => 'Knäböj',
            'user_id' => User::factory(),
            'result' => '150',
        ];
    }
}
