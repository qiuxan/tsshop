<?php

use Illuminate\Database\Seeder;

class UsersAddressTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Models\UserAddress::class,3)->create(['user_id'=>1]);
    }
}
