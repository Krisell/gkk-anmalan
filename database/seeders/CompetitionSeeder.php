<?php

namespace Database\Seeders;

use App\Competition;
use App\CompetitionRegistration;
use App\User;
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
            'date' => now()->addDays(10),
        ]);

        CompetitionRegistration::factory()
            ->for(User::whereEmail('sameday@example.com')->first())
            ->for($competition)
            ->create();

        Competition::factory(3)->create([
            'date' => now()->addDays(30),
        ]);
    }
}
