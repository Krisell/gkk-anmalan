<?php

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;

test('profile shows last helper date when user has recent helper activity', function () {
    $user = User::factory()->create([
        'created_at' => now()->subMonths(3), // Old enough to show warning
    ]);

    $event = Event::factory()->create([
        'date' => now()->subMonths(2),
    ]);

    $eventRegistration = EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'presence_confirmed' => true,
    ]);

    $this->actingAs($user);

    $response = $this->get('/profile');

    $response->assertSuccessful();
    $response->assertViewHas('lastHelperDate', $event->fresh()->date);
});

test('profile shows no last helper date when user has no confirmed presence', function () {
    $user = User::factory()->create([
        'created_at' => now()->subMonths(3),
    ]);

    $event = Event::factory()->create([
        'date' => now()->subMonths(2),
    ]);

    // Event registration without confirmed presence
    EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'presence_confirmed' => false,
    ]);

    $this->actingAs($user);

    $response = $this->get('/profile');

    $response->assertSuccessful();
    $response->assertViewHas('lastHelperDate', null);
});

test('profile shows no last helper date when user has no event registrations', function () {
    $user = User::factory()->create([
        'created_at' => now()->subMonths(3),
    ]);

    $this->actingAs($user);

    $response = $this->get('/profile');

    $response->assertSuccessful();
    $response->assertViewHas('lastHelperDate', null);
});

test('profile includes user with event registrations loaded', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $eventRegistration = EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
    ]);

    $this->actingAs($user);

    $response = $this->get('/profile');

    $response->assertSuccessful();
    $response->assertViewHas('user');

    $viewUser = $response->viewData('user');
    expect($viewUser->eventRegistrations)->not->toBeNull();
});
