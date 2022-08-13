<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Vanlig',
            'last_name' => 'Dödlig',
            'email' => 'user@example.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => null,
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'GKK',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => 'admin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'GKK',
            'last_name' => 'Superadmin',
            'email' => 'superadmin@example.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => 'superadmin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' => 'Krisell',
            'email' => 'martin.krisell@gmail.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => 'admin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' => 'Super',
            'email' => 'martinkrisell@gmail.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => 'superadmin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' => 'Normal',
            'email' => 'martinnormal@gmail.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => null,
            'granted_by' => 1,
        ]);
    }
}
