<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    $image = $faker->randomElement([
        "https://i.ebayimg.com/images/g/eEEAAOSw0AFcnLbw/s-l500.jpg",
        "https://i.ebayimg.com/images/g/-2IAAOSwVq9cwsZ~/s-l1600.jpg",
        "https://i.ebayimg.com/images/g/Fc4AAOSwuItcna1N/s-l1600.jpg",
        "https://i.ebayimg.com/images/g/m4kAAOSwGl9cna1O/s-l1600.jpg",
        "https://i.ebayimg.com/images/g/HtIAAOSwWnBcpy2C/s-l500.jpg",
        "https://i.ebayimg.com/images/g/Hp8AAOSwXgFcna1N/s-l1600.jpg",

    ]);

    return [
        //

        'title'        => $faker->word,
        'description'  => $faker->sentence,
        'image'        => $image,
        'on_sale'      => true,
        'rating'       => $faker->numberBetween(0, 5),
        'sold_count'   => 0,
        'review_count' => 0,
        'price'        => 0,
    ];
});
