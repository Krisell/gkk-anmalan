<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InactiveUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_inactive_user_is_redirected_to_an_information_page()
    {
        $user = User::factory()->create([
            'inactivated_at' => now(),
        ]);

        auth()->login($user);

        $this->get('/')->assertRedirect('/inactivated');
        $this->get('/events')->assertRedirect('/inactivated');
        $this->get('/competitions')->assertRedirect('/inactivated');
        $this->get('/documents')->assertRedirect('/inactivated');
    }

    /** @test */
    public function an_administrator_can_inactivate_a_user_account()
    {
        $user = User::factory()->create();

        $this->assertNull($user->inactivated_at);

        $normalUser = User::factory()->create(['role' => 'user']);
        auth()->login($normalUser);

        $this->post("/admin/accounts/inactivate/{$user->id}")->assertUnauthorized();

        $adminUser = User::factory()->create(['role' => 'admin']);
        auth()->login($adminUser);

        $this->post("/admin/accounts/inactivate/{$user->id}")->assertOk();
        $this->assertNotNull($user->fresh()->inactivated_at);
    }

    /** @test */
    public function an_administrator_can_reactivate_a_user_account()
    {
        $user = User::factory()->create([
            'inactivated_at' => now(),
        ]);

        $this->assertNotNull($user->inactivated_at);

        $normalUser = User::factory()->create(['role' => 'user']);
        auth()->login($normalUser);

        $this->post("/admin/accounts/reactivate/{$user->id}")->assertUnauthorized();

        $adminUser = User::factory()->create(['role' => 'admin']);
        auth()->login($adminUser);

        $this->post("/admin/accounts/reactivate/{$user->id}")->assertOk();
        $this->assertNull($user->fresh()->inactivated_at);
    }
}
