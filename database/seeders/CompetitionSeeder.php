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

        Competition::factory()->create([
            'name' => 'Veteran SM klassisk styrkelyft',
            'date' => now()->addDays(45),
            'end_date' => now()->addDays(47),
            'last_registration_at' => now()->addDays(20),
            'description' => 'Notera att beslut gällande ersättning av anmälningsavgift ännu inte är fastställt.',
            'pdf_url' => 'https://www.africau.edu/images/default/sample.pdf',
            'link_url' => 'https://data.styrkelyft.se/',
        ]);

        Competition::factory()->create([
            'name' => 'Götalandsmästerskapen KSL och (K)BP',
            'date' => now()->addDays(15),
            'end_date' => now()->addDays(16),
            'last_registration_at' => now()->subDays(2),
            'description' => 'Observera att det finns kvalgränser för KSL.',
            'pdf_url' => 'https://www.africau.edu/images/default/sample.pdf',
            'link_url' => 'https://data.styrkelyft.se/',
        ]);
    }
}
