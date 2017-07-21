<?php

$factory->define(Baytek\Laravel\Content\Models\Content::class, function (Faker\Generator $faker) {
    static $password;

    $title = $faker->sentence();

    return [
        'key' => str_slug($title),
        'title' => $title,
        'content' => $faker->paragraphs(rand(2, 10)),
    ];
});

$factory->define(Baytek\Laravel\Content\Models\ContentRelations::class, function (Faker\Generator $faker) {
    static $password;

    $title = $faker->sentence();

    return [
        'key' => str_slug($title),
        'title' => $title,
        'content' => $faker->paragraphs(rand(2, 10)),
    ];
});

$factory->define(Baytek\Laravel\Content\Models\ContentMeta::class, function (Faker\Generator $faker) {
    static $password;

    $title = $faker->sentence();

    return [
        'key' => str_slug($title),
        'title' => $title,
        'content' => $faker->paragraphs(rand(2, 10)),
    ];
});