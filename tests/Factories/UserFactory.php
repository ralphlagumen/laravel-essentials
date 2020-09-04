<?php

use Faker\Generator as Faker;
use Lagumen\LaravelEssential\Tests\Models\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name'   => $faker->name,
        'email'  => $faker->safeEmail,
        'active' => true,
    ];
});
