<?php

use App\Models\Competition;
use App\Models\Event;
use App\Models\EventRegistration;

it('can show all competitions for signed in users', function () {
    $this->get('/competitions')->assertRedirect('/login');

    login();

    $competition = Competition::factory()->create([
        'date' => now()->addDays(7),
        'publish_count' => true,
    ]);

    $this->get('/competitions')->assertSee($competition->name);
});

it('loads event registrations when viewing a competition', function () {
    $user = login();

    $competition = Competition::factory()->create([
        'date' => now()->addDays(7),
    ]);

    $event = Event::factory()->create([
        'date' => now()->subDays(100), // Within a year for helper count
    ]);

    EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'presence_confirmed' => true,
    ]);

    $response = $this->get("/competitions/{$competition->id}");

    $response->assertOk();
    $response->assertViewHas('user', function ($viewUser) {
        return $viewUser->relationLoaded('eventRegistrations') &&
               $viewUser->eventRegistrations->first()->relationLoaded('event');
    });
});
