<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function data($overrides = [])
    {
        return [
            'name' => 'Martin',
            'time' => '9–15',
            'date' => '2020-02-03 00:00:00',
            'location' => 'Friskis majorna',
            'description' => 'Alla behövs!',
            'show_status' => 'default',
        ] + $overrides;
    }

    /** @test */
    public function an_admin_can_create_an_event()
    {
        $user = User::factory()->create(['role' => 'admin']);
        auth()->login($user);

        $this->post('/admin/events', $this->data())->assertStatus(201);
        $this->assertDatabaseHas('events', $this->data());
    }

    /** @test */
    public function a_superadmin_can_create_an_event()
    {
        $user = User::factory()->create(['role' => 'superadmin']);
        auth()->login($user);

        $this->post('/admin/events', $this->data())->assertStatus(201);
        $this->assertDatabaseHas('events', $this->data());
    }

    /** @test */
    public function a_non_admin_cannot_create_an_event()
    {
        $this->post('/admin/events', $this->data())->assertStatus(302);

        $user = User::factory()->create();
        auth()->login($user);

        $this->post('/admin/events', $this->data())->assertStatus(401);
        $this->assertDatabaseMissing('events', $this->data());
    }

    /** @test */
    public function a_non_admin_cant_promote_users()
    {
        $user = User::factory()->create();
        $nonAdminUser = User::factory()->create(['role' => 'user']);
        auth()->login($nonAdminUser);

        $this->assertNull($user->role);

        $this->post("/admin/accounts/promote/{$user->id}")->assertStatus(401);
        $this->assertNull($user->fresh()->role);
    }

    /** @test */
    public function a_normal_admin_cant_promote_other_users()
    {
        $user = User::factory()->create();
        $adminUser = User::factory()->create(['role' => 'admin']);
        auth()->login($adminUser);

        $this->assertNull($user->role);

        $this->post("/admin/accounts/promote/{$user->id}")->assertStatus(401);
        $this->assertNull($user->fresh()->role);
    }

    /** @test */
    public function a_superadmin_can_promote_other_users()
    {
        $user = User::factory()->create();
        $adminUser = User::factory()->create(['role' => 'superadmin']);
        auth()->login($adminUser);

        $this->assertNull($user->role);

        $this->post("/admin/accounts/promote/{$user->id}")->assertStatus(200);
        $this->assertEquals('admin', $user->fresh()->role);
    }

    /** @test */
    public function a_non_admin_cant_demote_users()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $nonAdminUser = User::factory()->create(['role' => 'user']);
        auth()->login($nonAdminUser);

        $this->assertEquals('admin', $user->role);

        $this->post("/admin/accounts/demote/{$user->id}")->assertStatus(401);
        $this->assertEquals('admin', $user->fresh()->role);
    }

    /** @test */
    public function a_normal_admin_cant_demote_other_users()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $adminUser = User::factory()->create(['role' => 'admin']);
        auth()->login($adminUser);

        $this->assertEquals('admin', $user->role);

        $this->post("/admin/accounts/demote/{$user->id}")->assertStatus(401);
        $this->assertEquals('admin', $user->fresh()->role);
    }

    /** @test */
    public function a_superadmin_can_demote_other_users()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $adminUser = User::factory()->create(['role' => 'superadmin']);
        auth()->login($adminUser);

        $this->assertEquals('admin', $user->role);

        $this->post("/admin/accounts/demote/{$user->id}")->assertStatus(200);
        $this->assertNull($user->fresh()->role);
    }

    /** @test */
    public function only_an_admin_can_load_the_documents_admin_page_with_the_firebase_jwt()
    {
        $john = User::factory()->create();

        auth()->login($john);

        $this->get('/admin/documents')->assertUnauthorized();

        $admin = User::factory()->create(['role' => 'admin']);

        auth()->login($admin);

        $this->get('/admin/documents')->assertViewHas('jwt');
    }
}
