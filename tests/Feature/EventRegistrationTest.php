<?php

namespace Tests\Feature;

use App\User;
use App\Event;
use Carbon\Carbon;
use Tests\TestCase;
use App\EventRegistration;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_event_registration_can_be_created_given_the_required_data()
    {
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create();
        auth()->login($user);

        $this->post("/events/{$event->id}/registrations", ['status' => 1, 'comment' => 'abc'])->assertStatus(201);

        $this->assertDatabaseHas('event_registrations', ['status' => 1, 'comment' => 'abc']);
    }

    /** @test */
    public function a_competition_registration_can_be_updated_given_the_required_data()
    {
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create();
        auth()->login($user);

        $registration = factory(EventRegistration::class)->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $this->post("/events/{$event->id}/registrations", array_merge($registration->toArray(), [
            'comment' => 'NEW COMMENT',
        ]))->assertStatus(200);

        $this->assertEquals('NEW COMMENT', $registration->fresh()->comment);
    }
}
