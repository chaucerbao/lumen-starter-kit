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

$factory->define(App\PendingUpdate::class, function ($faker) {
    return [
    ];
});

$factory->define(App\Post::class, function ($faker) {
    return [
        'author_id' => factory(App\User::class)->create()->id,
        'slug' => $faker->unique()->slug,
        'title' => $faker->sentence,
        'body' => $faker->paragraph,
        'is_active' => $faker->boolean(),
        'published_at' => $faker->dateTime,
    ];
});

$factory->define(App\Role::class, function ($faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\Tag::class, function ($faker) {
    return [
        'slug' => $faker->unique()->slug,
        'name' => $faker->word,
    ];
});

$factory->define(App\User::class, function ($faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->email,
        'password' => str_random(16),
    ];
});
