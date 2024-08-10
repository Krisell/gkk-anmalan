<?php

use App\Competition;
use App\Event;
use Carbon\Carbon;

test('a competition has a date for last registration', function () {
    $competition = Competition::factory()->create();

    $this->assertNull($competition->last_registration_at);

    $competition->update(['last_registration_at' => '2020-02-02']);

    $this->assertNotNull($competition->fresh()->last_registration_at);
});

test('an event has a date for last registration', function () {
    $event = Event::factory()->create();

    $this->assertNull($event->last_registration_at);

    $event->update(['last_registration_at' => '2020-02-02']);

    $this->assertNotNull($event->fresh()->last_registration_at);
});

test('a competition registration can be created and update before the last date but not after', function () {
    $competition = Competition::factory()->create(['last_registration_at' => now()->addDays(2)]);
    login();

    $this->post("/competitions/{$competition->id}/registrations", [
        'licence_number' => 'ab',
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ])->assertStatus(201);

    $this->post("/competitions/{$competition->id}/registrations", [
        'licence_number' => 'cd',
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ])->assertStatus(200);

    $this->assertDatabaseHas('competition_registrations', ['licence_number' => 'cd']);
    $this->assertDatabaseMissing('competition_registrations', ['licence_number' => 'ab']);

    Carbon::setTestNow(now()->addDays(3));

    $this->post("/competitions/{$competition->id}/registrations", [
        'licence_number' => 'ef',
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ])->assertStatus(401);

    $this->post("/competitions/{$competition->id}/registrations", [
        'licence_number' => 'gh',
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ])->assertStatus(401);

    $this->assertDatabaseMissing('competition_registrations', ['licence_number' => 'ef']);
    $this->assertDatabaseMissing('competition_registrations', ['licence_number' => 'gh']);
});

test('an event registration can be created and update before the last date but not after', function () {
    $event = Event::factory()->create(['last_registration_at' => now()->addDays(2)]);

    login();

    $this->post("/events/{$event->id}/registrations", [
        'status' => true,
        'comment' => 'ab',
    ])->assertStatus(201);

    $this->post("/events/{$event->id}/registrations", [
        'status' => true,
        'comment' => 'cd',
    ])->assertStatus(200);

    $this->assertDatabaseHas('event_registrations', ['comment' => 'cd']);
    $this->assertDatabaseMissing('event_registrations', ['comment' => 'ab']);

    Carbon::setTestNow(now()->addDays(3));

    $this->post("/events/{$event->id}/registrations", [
        'status' => true,
        'comment' => 'ef',
    ])->assertStatus(401);

    $this->post("/events/{$event->id}/registrations", [
        'status' => true,
        'comment' => 'gh',
    ])->assertStatus(401);

    $this->assertDatabaseMissing('event_registrations', ['comment' => 'ef']);
    $this->assertDatabaseMissing('event_registrations', ['comment' => 'gh']);
});
