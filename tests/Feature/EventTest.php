<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function data($overrides = [])
    {
        return \array_merge([
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
    public function a_non_admin_cannot_create_an_event()
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

    /** @test */
    public function an_admin_cannot_see_old_events_by_default()
    {
        auth()->login(User::factory()->create(['role' => 'admin']));
        $event = Event::factory()->create(['name' => 'An old event', 'date' => now()->subDays(10)]);

        $this->get('admin/events')->assertDontSee('An old event');
    }

    /** @test */
    public function an_admin_cannot_see_old_events_via_a_query_parameter()
    {
        auth()->login(User::factory()->create(['role' => 'admin']));
        $event = Event::factory()->create(['name' => 'An old event', 'date' => now()->subDays(10)]);

        $this->get('admin/events?all=true')->assertSee('An old event');
    }
}
