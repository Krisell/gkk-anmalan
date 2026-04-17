<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $event = Event::factory()->create([
            'name' => 'DM KSL',
            'date' => now()->addDays(10),
        ]);

        EventRegistration::factory()
            ->for(User::whereEmail('sameday@example.com')->first())
            ->for($event)
            ->create();

        Event::factory(2)->create([
            'date' => now()->addDays(30),
        ]);

        $this->seedHelperCase('helper.ok@example.com', now()->subMonths(2));
        $this->seedHelperCase('helper.warning@example.com', now()->subDays(330));
        $this->seedHelperCase('helper.expired@example.com', now()->subMonths(14));
    }

    private function seedHelperCase(string $email, \DateTimeInterface $eventDate): void
    {
        $event = Event::factory()->create([
            'name' => 'Funktionärsaktivitet',
            'date' => $eventDate,
        ]);

        EventRegistration::factory()
            ->for(User::whereEmail($email)->first())
            ->for($event)
            ->create(['presence_confirmed' => true]);
    }
}
