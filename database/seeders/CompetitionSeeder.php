<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $competition = Competition::factory()->create([
            'name' => 'DM KSL',
            'date' => now()->addDays(10),
        ]);

        foreach ([
            'youth@example.com',
            'junior@example.com',
            'lok@example.com',
            'student@example.com',
            'sameday@example.com',
        ] as $email) {
            CompetitionRegistration::factory()
                ->for(User::whereEmail($email)->first())
                ->for($competition)
                ->create();
        }

        Competition::factory(3)->create([
            'name' => 'Some other competition',
            'date' => now()->addDays(30),
        ]);
    }
}
