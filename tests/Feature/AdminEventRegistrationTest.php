<?php

namespace Tests\Feature;

use App\User;
use App\Event;
use Tests\TestCase;
use App\EventRegistration;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminEventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_update_an_event_registration()
    {
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create(['role' => 'admin']);

        $registration = factory(EventRegistration::class)->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 0,
            'comment' => 'No comment'
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
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create();

        $registration = factory(EventRegistration::class)->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 0,
            'comment' => 'No comment'
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
