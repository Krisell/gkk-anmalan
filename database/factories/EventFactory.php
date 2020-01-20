<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Event;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
      'name' => $faker->name(),
    ];
});
