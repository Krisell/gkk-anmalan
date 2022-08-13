<?php

namespace Tests\Feature;

use App\Event;
use App\EventRegistration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_event_registration_can_be_created_given_the_required_data()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();
        auth()->login($user);

        $this->post("/events/{$event->id}/registrations", ['status' => 1, 'comment' => 'abc'])->assertStatus(201);

        $this->assertDatabaseHas('event_registrations', ['status' => 1, 'comment' => 'abc']);
    }

    /** @test */
    public function a_competition_registration_can_be_updated_given_the_required_data()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();
        auth()->login($user);

        $registration = EventRegistration::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $this->post("/events/{$event->id}/registrations", \array_merge($registration->toArray(), [
            'comment' => 'NEW COMMENT',
        ]))->assertStatus(200);

        $this->assertEquals('NEW COMMENT', $registration->fresh()->comment);
    }
}
