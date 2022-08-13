<?php

namespace Tests\Feature;

use App\Event;
use App\EventRegistration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_update_an_event_registration()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create(['role' => 'admin']);

        $registration = EventRegistration::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 0,
            'comment' => 'No comment',
        ]);

        $data = [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 1,
            'status' => 'Another comment',
        ];

        auth()->login($user);
        $this->post("/events/{$event->id}/registrations/{$registration->id}", $data)->assertStatus(200);

        $this->assertDatabaseHas('event_registrations', $data);
    }

    /** @test */
    public function a_non_admin_cant_update_a_registration()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();

        $registration = EventRegistration::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 0,
            'comment' => 'No comment',
        ]);

        $data = [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 1,
            'status' => 'Another comment',
        ];

        $this->post("/events/{$event->id}/registrations/{$registration->id}", $data)->assertStatus(302);

        auth()->login($user);

        $this->post("/events/{$event->id}/registrations/{$registration->id}", $data)->assertStatus(401);

        $this->assertDatabaseMissing('event_registrations', $data);
    }
}
