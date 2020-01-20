<?php

namespace Tests\Feature;

use App\User;
use App\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_may_delete_an_event()
    {
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create(['role' => 'admin']);

        auth()->login($user);

        $this->delete("/admin/events/{$event->id}")->assertOk();

        $this->assertCount(0, Event::all());
    }

    /** @test */
    public function an_event_is_only_soft_deleted_by_default()
    {
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create(['role' => 'admin']);

        auth()->login($user);

        $this->delete("/admin/events/{$event->id}")->assertOk();

        $this->assertCount(1, Event::withTrashed()->get());
    }
}
