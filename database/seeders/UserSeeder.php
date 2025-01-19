<?php

namespace Database\Seeders;

use App\Models\User;
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
            'last_name' => 'Användare',
            'birth_year' => 1990,
            'email' => 'user@example.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => null,
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'GKK',
            'last_name' => 'Admin',
            'birth_year' => 1987,
            'email' => 'admin@example.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => 'admin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'GKK',
            'last_name' => 'Superadmin',
            'birth_year' => 1970,
            'email' => 'superadmin@example.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => 'superadmin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'Annan',
            'last_name' => 'Admin',
            'birth_year' => 1987,
            'email' => 'annan.admin@example.com',
            'password' => '$2y$04$ZanbmQomv/1y7qYst7tJW.1PdfM6MpTYIUSEWB9wlMfI/rRNQfgmq', // asdasdasd
            'role' => 'admin',
            'granted_by' => 1,
        ]);

        User::factory()->create(['email' => 'sameday@example.com']);
        User::factory()->honorary()->create(['email' => 'honorary@example.com', 'first_name' => 'Hedersmedlem', 'last_name' => 'Hedersmedlem']);

        User::factory()->create([
            'first_name' => 'Youth',
            'last_name' => 'Member',
            'birth_year' => now()->subYears(15)->year,
            'email' => 'youth@example.com',
        ]);

        User::factory()->create([
            'first_name' => 'Junior',
            'last_name' => 'Member',
            'birth_year' => now()->subYears(20)->year,
            'email' => 'junior@example.com',
        ]);

        User::factory()->create([
            'first_name' => 'Lok',
            'last_name' => 'Member',
            'birth_year' => now()->subYears(24)->year,
            'email' => 'lok@example.com',
        ]);

        User::factory()->create([
            'first_name' => 'Student',
            'last_name' => 'Member',
            'birth_year' => now()->subYears(30)->year,
            'email' => 'student@example.com',
            'is_student_over_23' => true,
        ]);

        User::factory()->create([
            'first_name' => 'Adult',
            'last_name' => 'Member',
            'birth_year' => now()->subYears(30)->year,
            'email' => 'adult@example.com',
        ]);

        User::factory()->create([
            'first_name' => 'Senior',
            'last_name' => 'Member',
            'birth_year' => now()->subYears(65)->year,
            'email' => 'senior@example.com',
        ]);
    }
}
