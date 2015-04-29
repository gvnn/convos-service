<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();
        User::create(['email' => 'foo@domain.com', 'password' => Hash::make('test')]);
        User::create(['email' => 'bar@domain.com', 'password' => Hash::make('test')]);
    }
}