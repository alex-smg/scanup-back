<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Response;
use Faker\Generator as Faker;

$factory->define(Response::class, function (Faker $faker) {
    return [
        'text' => $faker->text($maxNbChars = 124),
        'question_id' => factory('App\Question')->create()->id
    ];
});
