<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();
        // Create few users for testing

        for ($i = 0; $i <= 10; $i++) {
            User::create([
                'email' => $faker->email,
                'token' => $faker->sha1
            ]);
        }
        
    }
}