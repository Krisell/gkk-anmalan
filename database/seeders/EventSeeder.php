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
    }
}
