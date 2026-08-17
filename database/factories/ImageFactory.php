<?php
use App\User;
use App\Models\Image;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'user_id' => User::all()->random()->id,
        'image_path' => \App\Helpers\ImagePlaceholder::make(640, 480, storage_path('app/images')),
        'description' => $faker->text(200)
    ];
});
