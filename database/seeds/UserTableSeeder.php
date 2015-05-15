<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->delete();

        User::create([
            'first_name' => 'Site',
            'last_name' => 'Administrator',
            'email' => 'admin@site.com',
            'password' => 'secret',
        ]);
    }
}
