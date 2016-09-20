<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Crawler::class, function (Faker\Generator $faker) {

    return [
        'pivotal_id' => $faker->randomNumber + rand(10, 100),
        'title' => $faker->name,
        'point' => $faker->randomDigit,
        'project_id' => $faker->randomNumber,
        'story_type' => $faker->word,
        'last_updated_at' => $faker->iso8601(),
    ];
});
