<?php

use App\Event;

test('an admin may delete an event', function () {
    $event = Event::factory()->create();
    loginAdmin();

    $this->delete("/admin/events/{$event->id}")->assertOk();

    $this->assertCount(0, Event::all());
});

test('an event is only soft deleted by default', function () {
    $event = Event::factory()->create();
    loginAdmin();

    $this->delete("/admin/events/{$event->id}")->assertOk();

    $this->assertCount(1, Event::withTrashed()->get());
});
