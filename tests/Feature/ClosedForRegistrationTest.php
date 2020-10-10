<?php

namespace Tests\Feature;

use App\User;
use App\Event;
use Carbon\Carbon;
use Tests\TestCase;
use App\Competition;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClosedForRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_competition_has_a_date_for_last_registration()
    {
        $competition = Competition::factory()->create();

        $this->assertNull($competition->last_registration_at);

        $competition->update(['last_registration_at' => '2020-02-02']);

        $this->assertNotNull($competition->fresh()->last_registration_at);
    }

    /** @test */
    public function an_event_has_a_date_for_last_registration()
    {
        $event = Event::factory()->create();

        $this->assertNull($event->last_registration_at);

        $event->update(['last_registration_at' => '2020-02-02']);

        $this->assertNotNull($event->fresh()->last_registration_at);
    }

    /** @test */
    public function a_competition_registration_can_be_created_and_update_before_the_last_date_but_not_after()
    {
        $competition = Competition::factory()->create(['last_registration_at' => now()->addDays(2)]);
        $user = User::factory()->create();
        auth()->login($user);

        $this->post("/competitions/{$competition->id}/registrations", [
            'licence_number' => 'ab',
            'weight_class' => '74',
            'gender' => 'Män',
            'events' => json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
            'status' => 1,
        ])->assertStatus(201);

        $this->post("/competitions/{$competition->id}/registrations", [
            'licence_number' => 'cd',
            'weight_class' => '74',
            'gender' => 'Män',
            'events' => json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
            'status' => 1,
        ])->assertStatus(200);

        $this->assertDatabaseHas('competition_registrations', ['licence_number' => 'cd']);
        $this->assertDatabaseMissing('competition_registrations', ['licence_number' => 'ab']);

        Carbon::setTestNow(now()->addDays(3));

        $this->post("/competitions/{$competition->id}/registrations", [
            'licence_number' => 'ef',
            'weight_class' => '74',
            'gender' => 'Män',
            'events' => json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
            'status' => 1,
        ])->assertStatus(401);

        $this->post("/competitions/{$competition->id}/registrations", [
            'licence_number' => 'gh',
            'weight_class' => '74',
            'gender' => 'Män',
            'events' => json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
            'status' => 1,
        ])->assertStatus(401);

        $this->assertDatabaseMissing('competition_registrations', ['licence_number' => 'ef']);
        $this->assertDatabaseMissing('competition_registrations', ['licence_number' => 'gh']);
    }

    /** @test */
    public function an_event_registration_can_be_created_and_update_before_the_last_date_but_not_after()
    {
        $event = Event::factory()->create(['last_registration_at' => now()->addDays(2)]);

        $user = User::factory()->create();
        auth()->login($user);

        $this->post("/events/{$event->id}/registrations", [
            'status' => 1,
            'comment' => 'ab',
        ])->assertStatus(201);

        $this->post("/events/{$event->id}/registrations", [
            'status' => 1,
            'comment' => 'cd',
        ])->assertStatus(200);

        $this->assertDatabaseHas('event_registrations', ['comment' => 'cd']);
        $this->assertDatabaseMissing('event_registrations', ['comment' => 'ab']);

        Carbon::setTestNow(now()->addDays(3));

        $this->post("/events/{$event->id}/registrations", [
            'status' => 1,
            'comment' => 'ef',
        ])->assertStatus(401);

        $this->post("/events/{$event->id}/registrations", [
            'status' => 1,
            'comment' => 'gh',
        ])->assertStatus(401);

        $this->assertDatabaseMissing('event_registrations', ['comment' => 'ef']);
        $this->assertDatabaseMissing('event_registrations', ['comment' => 'gh']);
    }
}
