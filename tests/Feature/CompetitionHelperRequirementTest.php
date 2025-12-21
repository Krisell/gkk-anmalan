<?php

use App\Models\Competition;
use App\Models\Event;
use App\Models\EventRegistration;

test('a user without helper activity and explicit_registration_approval=false cannot register for competition', function () {
    $competition = Competition::factory()->create();
    $user = login();
    $user->update(['explicit_registration_approval' => false]);

    $data = [
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ];

    $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(403);
});

test('a user without confirmed helper activity and explicit_registration_approval=false cannot register for competition', function () {
    $competition = Competition::factory()->create();
    $user = login();
    $user->update(['explicit_registration_approval' => false]);

    $event = Event::factory()->create(['date' => now()->subDays(10)]);
    EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => true,
        'presence_confirmed' => false,
    ]);

    $data = [
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ];

    $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(403);
});

test('a user with recent helper activity and explicit_registration_approval=false can register for competition', function () {
    $competition = Competition::factory()->create();
    $user = login();
    $user->update(['explicit_registration_approval' => false]);

    $event = Event::factory()->create(['date' => now()->subDays(200)]);
    EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => true,
        'presence_confirmed' => true,
    ]);

    $data = [
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ];

    $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(200);
});

test('a user without recent helper activity and explicit_registration_approval=false cannot register for competition', function () {
    $competition = Competition::factory()->create();
    $user = login();
    $user->update(['explicit_registration_approval' => false]);

    $event = Event::factory()->create(['date' => now()->subDays(400)]);
    EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => true,
        'presence_confirmed' => true,
    ]);

    $data = [
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ];

    $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(403);
});

test('a user without helper activity but explicit_registration_approval=true can register for competition', function () {
    $competition = Competition::factory()->create();
    $user = login();
    $user->update(['explicit_registration_approval' => true]);

    $data = [
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ];

    $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(200);
});

test('declining a competition should always work regardless of helper status', function () {
    $competition = Competition::factory()->create();
    $user = login();
    $user->update(['explicit_registration_approval' => false]);

    $data = [
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => false,
    ];

    $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(200);
});
