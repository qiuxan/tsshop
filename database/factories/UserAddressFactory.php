<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserAddress;
use Faker\Generator as Faker;

$factory->define(UserAddress::class, function (Faker $faker) {
    $addresses = [
        ["Berwick", "Victory"],
        ["Newstead", "Tasmania"],
        ["Sydney", "New South Wales"],
    ];

    $address=$faker->randomElement($addresses);

    return [
        'city'=>$address[0],
        'state'=>$address[1],
        'address'=>sprintf('No. %d of %d Street',$faker->randomNumber(2),$faker->randomNumber(3)),
        'post_code'=>$faker->postcode,
        'contact_name'=>$faker->name,
        'contact_phone'=>$faker->phoneNumber,


        //
    ];
});
