<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Event;
use Faker\Generator as Faker;
use App\EventRegistration;

$factory->define(EventRegistration::class, function (Faker $faker) {
    return [
      'status' => 1,
    ];
});
