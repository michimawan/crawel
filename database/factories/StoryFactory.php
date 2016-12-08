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
	$id = Carbon::now()->timestamp;
	$modNumber = 9999;
	$id = $id + $faker->randomNumber + rand(10, 100);
	$fixId = $id % rand(10, $modNumber);

    return [
        'pivotal_id' => $fixId,
        'title' => $faker->name,
        'point' => $faker->randomDigit,
        'project_id' => $faker->randomNumber,
        'story_type' => $faker->randomElement(['bug', 'feature', 'chore']),
    ];
});
