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
use Carbon\Carbon;

$factory->define(App\Models\Revision::class, function (Faker\Generator $faker) {
    $rand = rand(0, 1000);
    return [
        'child_tag_revisions' => $rand . Carbon::now()->toDateTimeString(),
        'end_time_check_story' => Carbon::now()->toDateTimeString(),
        'end_time_run_automate_test' => Carbon::now()->toDateTimeString(),
        'time_get_canary' => Carbon::now()->toDateTimeString(),
        'time_to_elb' => Carbon::now()->toDateTimeString(),
        'description' => $faker->text,
        'project' => $faker->word,
    ];
});
