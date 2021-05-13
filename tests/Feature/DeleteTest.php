<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_may_delete_an_event()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create(['role' => 'admin']);

        auth()->login($user);

        $this->delete("/admin/events/{$event->id}")->assertOk();

        $this->assertCount(0, Event::all());
    }

    /** @test */
    public function an_event_is_only_soft_deleted_by_default()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create(['role' => 'admin']);

        auth()->login($user);

        $this->delete("/admin/events/{$event->id}")->assertOk();

        $this->assertCount(1, Event::withTrashed()->get());
    }
}
