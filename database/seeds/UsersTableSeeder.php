<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $user=User::where('email','qiuxan@gmail.com')->first();

        if(!$user){
            User::create([
                    'name'=>'Xian QIu',
                    'email'=>'qiuxan@gmail.com',
                    'password'=>Hash::make('password'),
                    'email_verified_at'=>now()
                ]
            );

        }
    }
}
