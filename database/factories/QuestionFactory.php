<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Question;
use Faker\Generator as Faker;

$factory->define(Question::class, function (Faker $faker) {
    return [
        'title' => $faker->text($maxNbChars = 250),
        'image' => $faker->imageUrl($width = 640, $height = 480),
        'multi_choice' => $faker->boolean($chanceOfGettingTrue = 50),
        'survey_id' => factory('App\Survey')->create()->id
    ];
});
