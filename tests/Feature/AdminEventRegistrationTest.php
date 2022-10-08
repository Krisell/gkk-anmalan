<?php

namespace Tests\Feature;

use App\Competition;
use App\CompetitionRegistration;
use App\Event;
use App\EventRegistration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_admin_can_see_the_event_registration_list()
    {
        $event = Event::factory()->create();
        $userA = User::factory()->create(['role' => 'admin']);
        $userB = User::factory()->create();

        $remaining = User::factory(3)->create();

        EventRegistration::factory()->for($userA)->for($event)->create();
        EventRegistration::factory()->for($userB)->for($event)->create();

        auth()->login($userA);
        $this->get("/admin/events/$event->id")->assertViewHas([
            'event' => $event->load('registrations.user'),
            'remainingUsers' => User::orderBy('first_name', 'asc')->whereIn('id', $remaining->pluck('id'))->get(),
        ]);
    }

    /** @test */
    public function lifters_competing_the_same_day_are_marked()
    {
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
        $notCompeting = CompetitionRegistration::factory()->for($competitonC)->create(['status' => 0])->user_id;

        // $this->withoutExceptionHandling();
        auth()->login($user);
        $competingUsers = $this->get("/admin/events/$event->id")->viewData('competingUsers');

        $this->assertCount(3, $competingUsers);
        $this->assertEquals($userC, $competingUsers[1]);
        $this->assertEquals($userB, $competingUsers[2]);
        $this->assertEquals($userA, $competingUsers[0]);
    }

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

    /** @test */
    public function an_admin_can_add_an_additional_user_to_an_event()
    {
        $event = Event::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $this->assertDatabaseMissing('event_registrations', ['user_id' => $user->id, 'event_id' => $event->id]);

        auth()->login($admin);
        $this->post("/admin/events/{$event->id}/registrations/", ['user_id' => $user->id])->assertStatus(200);

        $this->assertDatabaseHas('event_registrations', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 1,
            'presence_confirmed' => 1,
        ]);
    }
}
