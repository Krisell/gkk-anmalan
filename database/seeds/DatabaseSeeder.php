<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Martin',
            'last_name' =>  'Krisell',
            'email' => 'martin.krisell@gmail.com',
            'password' => Hash::make('asdasd'),
            'role' => 'admin',
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' =>  'Super',
            'email' => 'martinkrisell@gmail.com',
            'password' => Hash::make('asdasd'),
            'role' => 'superadmin',
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' =>  'Normal',
            'email' => 'martinnormal@gmail.com',
            'password' => Hash::make('asdasd'),
            'role' => null,
        ]);
    }
}
