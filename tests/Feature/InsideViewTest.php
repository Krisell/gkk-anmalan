<?php

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;

test('inside view includes user with event_registrations', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $eventRegistration = EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
    ]);

    $this->actingAs($user);

    $response = $this->get('/insidan');

    $response->assertSuccessful();
    $response->assertViewIs('inside');
    $response->assertViewHas('user');

    $viewUser = $response->viewData('user');
    expect($viewUser)->not->toBeNull();
    expect($viewUser->eventRegistrations)->not->toBeNull();
    expect($viewUser->eventRegistrations)->toHaveCount(1);
    expect($viewUser->eventRegistrations->first()->event)->not->toBeNull();
});

test('inside view works when user has no event registrations', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get('/insidan');

    $response->assertSuccessful();
    $response->assertViewIs('inside');
    $response->assertViewHas('user');

    $viewUser = $response->viewData('user');
    expect($viewUser)->not->toBeNull();
    expect($viewUser->eventRegistrations)->not->toBeNull();
    expect($viewUser->eventRegistrations)->toHaveCount(0);
});
