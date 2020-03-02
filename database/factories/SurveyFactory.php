<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Survey;
use Faker\Generator as Faker;

$status = ['draft', 'disable', 'in progress'];
$isMysteryBrand = [true, false];

$factory->define(Survey::class, function (Faker $faker) use ($status, $isMysteryBrand) {
    return [
        'title' => $faker->text($maxNbChars = 200),
        'status' => $status[rand(0, 2)],
        'started_at' => $faker->iso8601($max = 'now'),
        'ended_at' => $faker->iso8601(),
        'is_mystery_brand' => $isMysteryBrand[rand(0, 1)],
        'description' => $faker->text($maxNbChars = 250),
        'brand_id' => factory('App\Organization')->create()->id
    ];
});
