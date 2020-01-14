<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function data ($overrides = []) {
        return [
            'name' => 'Martin',
            'time' => '9–15',
            'date' => '2020-02-03',
            'location' => 'Friskis majorna',
            'description' => 'Alla behövs!',
        ] + $overrides;
    }

    /** @test */
    function an_admin_can_create_an_event()
    {
        $user = factory(User::class)->create(['role' => 'admin']);
        auth()->login($user);

        $this->post('/admin/events', $this->data())->assertStatus(201);
        $this->assertDatabaseHas('events', $this->data());
    }

    /** @test */
    function a_non_admin_cannot_create_an_event()
    {
        $this->post('/admin/events', $this->data())->assertStatus(302);

        $user = factory(User::class)->create();
        auth()->login($user);

        $this->post('/admin/events', $this->data())->assertStatus(401);
        $this->assertDatabaseMissing('events', $this->data());
    }
}
