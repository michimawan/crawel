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

$factory->define(App\Models\Tag::class, function (Faker\Generator $faker) {

    return [
        'code' => $faker->randomNumber + rand(10, 100),
        'timing' => Carbon::now()->toDateTimeString(),
        'project' => $faker->word,
    ];
});
