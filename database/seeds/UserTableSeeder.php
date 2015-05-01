<?php

use Illuminate\Database\Seeder;
use App\Model\User;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();
        User::create(['name' => 'foo', 'email' => 'foo@domain.com', 'password' => Hash::make('test')]);
        User::create(['name' => 'bar', 'email' => 'bar@domain.com', 'password' => Hash::make('test')]);
    }
}