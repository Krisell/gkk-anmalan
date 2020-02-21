<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Competition;
use Faker\Generator as Faker;
use App\CompetitionRegistration;

$factory->define(CompetitionRegistration::class, function (Faker $faker) {
    return [
      'competition_id' => factory(Competition::class),
      'user_id' => factory(User::class),
      'licence_number' => '010101ab',
      'gender' => 'Män',
      'weight_class' => '74',
      'events' => json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
      'status' => 1,
    ];
});
