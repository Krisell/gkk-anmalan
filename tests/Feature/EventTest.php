<?php

use App\Event;

function eventData($overrides = [])
{
    return \array_merge([
        'name' => 'Eventnamn',
        'time' => '9–13',
        'date' => '2020-02-23 00:00:00',
        'location' => 'Hos mig',
        'description' => 'Det blir kul!',
        'publish_count' => false,
        'publish_list' => false,
        'show_status' => 'default',
    ], $overrides);
}

test('an admin can create an event', function () {
    loginAdmin();

    $this->post('/admin/events', eventData())->assertCreated();

    $this->assertDatabaseHas('events', eventData());
});

test('the event name is required', function () {
    loginAdmin();

    $this->post('/admin/events', eventData(['name' => null]))->assertSessionHasErrors('name');

    $this->assertCount(0, Event::all());
});

test('a non admin cannot create an event', function () {
    $this->post('/admin/events', eventData())->assertStatus(302);

    login();

    $this->post('/admin/events', eventData())->assertStatus(401);
    $this->assertDatabaseMissing('events', eventData());
});

test('an admin can update an an event', function () {
    loginAdmin();

    $event = Event::factory()->create();

    $this->patch("/admin/events/{$event->id}", eventData([
        'name' => 'Nytt namn',
        'publish_count' => true,
    ]))->assertOk();

    $this->assertDatabaseHas('events', eventData(['name' => 'Nytt namn', 'publish_count' => true]));
});

test('an admin cannot see old events by default', function () {
    loginAdmin();

    Event::factory()->create(['name' => 'An old event', 'date' => now()->subDays(10)]);

    $this->get('admin/events')->assertDontSee('An old event');
});

test('an admin cannot see old events via a query parameter', function () {
    loginAdmin();

    Event::factory()->create(['name' => 'An old event', 'date' => now()->subDays(10)]);

    $this->get('admin/events?all=true')->assertSee('An old event');
});
