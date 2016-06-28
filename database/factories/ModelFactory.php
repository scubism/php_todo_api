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

$factory->define(App\DataAccess\Eloquent\Todo::class, function ($faker) {
    return [
        'title' => $faker->firstNameMale(),
        'color' => $faker->hexcolor,
        'todo_groups_id' => 1
    ];
});
