<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Organization;
use Faker\Generator as Faker;

$status = ['company', 'brand', 'organization'];


$factory->define(Organization::class, function (Faker $faker) use ($status) {
    return [
        'name' => $faker->name,
        'description' => $faker->text($maxNbChars = 200),
        'logo' => $faker->imageUrl($width = 640, $height = 480),
        'status' => $status[rand(0, 2)]
    ];
});
