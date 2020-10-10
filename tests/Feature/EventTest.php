<?php

namespace Tests\Feature;

use App\User;
use App\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function data($overrides = [])
    {
        return array_merge([
            'name' => 'Eventnamn',
            'time' => '9–13',
            'date' => '2020-02-23',
            'location' => 'Hos mig',
            'description' => 'Det blir kul!',
            'publish_count' => false,
            'publish_list' => false,
            'show_status' => 'default',
        ], $overrides);
    }

    /** @test */
    public function an_admin_can_create_an_event()
    {
        auth()->login(User::factory()->create(['role' => 'admin']));

        $this->post('/admin/events', $this->data())->assertCreated();

        $this->assertDatabaseHas('events', $this->data());
    }

    /** @test */
    public function the_event_name_is_required()
    {
        auth()->login(User::factory()->create(['role' => 'admin']));

        $this->post('/admin/events', $this->data(['name' => null]))->assertSessionHasErrors('name');

        $this->assertCount(0, Event::all());
    }

    /** @test */
    function a_non_admin_cannot_create_an_event()
    {
        $this->post('/admin/events', $this->data())->assertStatus(302);

        auth()->login(User::factory()->create());

        $this->post('/admin/events', $this->data())->assertStatus(401);
        $this->assertDatabaseMissing('events', $this->data());
    }

    /** @test */
    public function an_admin_can_update_an_an_event()
    {
        auth()->login(User::factory()->create(['role' => 'admin']));
        $event = Event::factory()->create();

        $this->patch("/admin/events/{$event->id}", $this->data([
            'name' => 'Nytt namn',
            'publish_count' => true,
        ]))->assertOk();

        $this->assertDatabaseHas('events', $this->data(['name' => 'Nytt namn', 'publish_count' => true]));
    }
}
