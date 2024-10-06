<?php

use App\Models\Event;
use App\Models\EventRegistration;

test('an event registration can be created given the required data', function () {
    $event = Event::factory()->create();
    login();

    $this->post("/events/{$event->id}/registrations", ['status' => true, 'comment' => 'abc'])->assertStatus(201);

    $this->assertDatabaseHas('event_registrations', ['status' => true, 'comment' => 'abc']);
});

test('a competition registration can be updated given the required data', function () {
    $event = Event::factory()->create();
    $user = login();

    $registration = EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
    ]);
    $this->post("/events/{$event->id}/registrations", \array_merge($registration->toArray(), [
        'comment' => 'NEW COMMENT',
    ]))->assertStatus(200);

    $this->assertEquals('NEW COMMENT', $registration->fresh()->comment);
});
