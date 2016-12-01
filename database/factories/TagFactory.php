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
    $str = Carbon::now()->toDateTimeString();
    return [
        'code' => $faker->randomNumber . $str,
        'timing' => Carbon::now()->toDateTimeString(),
        'project' => $faker->word,
    ];
});
