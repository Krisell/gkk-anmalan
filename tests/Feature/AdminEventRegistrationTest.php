<?php

use App\ActivityLog;
use App\Competition;
use App\CompetitionRegistration;
use App\Event;
use App\EventRegistration;
use App\User;

test('the admin can see the event registration list', function () {
    $event = Event::factory()->create();
    $userA = User::factory()->create(['role' => 'admin']);
    $userB = User::factory()->create();

    $remaining = User::factory(3)->create();

    EventRegistration::factory()->for($userA)->for($event)->create();
    EventRegistration::factory()->for($userB)->for($event)->create();

    login($userA);
    $this->get("/admin/events/$event->id")->assertViewHas([
        'event' => $event->load('registrations.user'),
        'remainingUsers' => User::orderBy('first_name', 'asc')->whereIn('id', $remaining->pluck('id'))->get(),
    ]);
});

test('lifters competing the same day are marked', function () {
    $event = Event::factory()->create(['date' => now()->addDays(10)]);
    $user = User::factory()->create(['role' => 'admin']);

    EventRegistration::factory()->for($event)->create();
    EventRegistration::factory()->for($event)->create();

    $competitonA = Competition::factory()->create(['date' => $event->date]);
    $userA = CompetitionRegistration::factory()->for($competitonA)->create()->user_id;

    $competitonB = Competition::factory()->create(['date' => $event->date->copy()->subDay(), 'end_date' => $event->date]);
    $userB = CompetitionRegistration::factory()->for($competitonB)->create()->user_id;

    $competitonC = Competition::factory()->create(['date' => $event->date, 'end_date' => $event->date->copy()->addDay()]);
    $userC = CompetitionRegistration::factory()->for($competitonC)->create()->user_id;
    CompetitionRegistration::factory()->for($competitonC)->create(['status' => 0])->user_id;

    login($user);
    $competingUsers = $this->get("/admin/events/$event->id")->viewData('competingUsers');

    $this->assertCount(3, $competingUsers);
    $this->assertEquals($userC, $competingUsers[1]);
    $this->assertEquals($userB, $competingUsers[2]);
    $this->assertEquals($userA, $competingUsers[0]);
});

test('an admin can update an event registration', function () {
    $event = Event::factory()->create();
    $user = User::factory()->create(['role' => 'admin']);

    $registration = EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => false,
        'comment' => 'No comment',
    ]);

    $data = [
        'event_id' => $event->id,
        'user_id' => $user->id,
        'status' => true,
        'status' => 'Another comment',
    ];

    login($user);
    $this->post("/events/{$event->id}/registrations/{$registration->id}", $data)->assertStatus(200);

    $this->assertDatabaseHas('event_registrations', $data);
});

test('a non admin cant update a registration', function () {
    $event = Event::factory()->create();
    $user = User::factory()->create();

    $registration = EventRegistration::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => false,
        'comment' => 'No comment',
    ]);

    $data = [
        'event_id' => $event->id,
        'user_id' => $user->id,
        'status' => true,
        'status' => 'Another comment',
    ];

    $this->post("/events/{$event->id}/registrations/{$registration->id}", $data)->assertStatus(302);

    login($user);

    $this->post("/events/{$event->id}/registrations/{$registration->id}", $data)->assertStatus(401);

    $this->assertDatabaseMissing('event_registrations', $data);
});

test('an admin can add an additional user to an event', function () {
    $event = Event::factory()->create();
    $user = User::factory()->create();

    $this->assertDatabaseMissing('event_registrations', ['user_id' => $user->id, 'event_id' => $event->id]);

    loginAdmin();
    $this->post("/admin/events/{$event->id}/registrations/", ['user_id' => $user->id])->assertStatus(200);

    $this->assertDatabaseHas('event_registrations', [
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => true,
        'presence_confirmed' => 1,
    ]);

    $this->assertDatabaseHas(ActivityLog::class, [
        'performed_by' => auth()->id(),
        'user_id' => $user->id,
        'action' => 'event-registration',
        'data' => json_encode([
            'event_id' => $event->id,
        ]),
    ]);
});
