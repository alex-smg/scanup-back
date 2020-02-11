<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Survey;
use Faker\Generator as Faker;

$status = ['draft', 'disable', 'in progress'];

$factory->define(Survey::class, function (Faker $faker) use ($status) {
    return [
        'image' => $faker->imageUrl($width = 640, $height = 480),
        'title' => $faker->text($maxNbChars = 200),
        'status' => $status[rand(0, 2)],
        'description' => $faker->text($maxNbChars = 250),
        'brand_id' => factory('App\Organization')->create()->id
    ];
});
