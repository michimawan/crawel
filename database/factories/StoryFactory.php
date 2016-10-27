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

use App\Lib\Helper;
use Carbon\Carbon;

$factory->define(App\Models\Story::class, function (Faker\Generator $faker) {
	$date = Helper::sanitizeDate(Carbon::today()->toDateTimeString(), ' ');

    return [
        'pivotal_id' => $faker->randomNumber + rand(10, 100),
        'title' => $faker->name,
        'point' => $faker->randomDigit,
        'project_id' => $faker->randomNumber,
        'story_type' => $faker->word,
    ];
});
