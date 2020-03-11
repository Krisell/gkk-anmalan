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
            'show_status' => 'default',
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
    function a_superadmin_can_create_an_event()
    {
        $user = factory(User::class)->create(['role' => 'superadmin']);
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

    /** @test */
    function a_non_admin_cant_promote_users()
    {
        $user = factory(User::class)->create();
        $nonAdminUser = factory(User::class)->create(['role' => 'user']);
        auth()->login($nonAdminUser);

        $this->assertNull($user->role);

        $this->post("/admin/accounts/promote/{$user->id}")->assertStatus(401);
        $this->assertNull($user->fresh()->role);
    }

    /** @test */
    function a_normal_admin_cant_promote_other_users()
    {
        $user = factory(User::class)->create();
        $adminUser = factory(User::class)->create(['role' => 'admin']);
        auth()->login($adminUser);

        $this->assertNull($user->role);

        $this->post("/admin/accounts/promote/{$user->id}")->assertStatus(401);
        $this->assertNull($user->fresh()->role);
    }

    /** @test */
    function a_superadmin_can_promote_other_users()
    {
        $user = factory(User::class)->create();
        $adminUser = factory(User::class)->create(['role' => 'superadmin']);
        auth()->login($adminUser);

        $this->assertNull($user->role);

        $this->post("/admin/accounts/promote/{$user->id}")->assertStatus(200);
        $this->assertEquals('admin', $user->fresh()->role);
    }

    /** @test */
    function a_non_admin_cant_demote_users()
    {
        $user = factory(User::class)->create(['role' => 'admin']);
        $nonAdminUser = factory(User::class)->create(['role' => 'user']);
        auth()->login($nonAdminUser);

        $this->assertEquals('admin', $user->role);

        $this->post("/admin/accounts/demote/{$user->id}")->assertStatus(401);
        $this->assertEquals('admin', $user->fresh()->role);
    }

   /** @test */
   function a_normal_admin_cant_demote_other_users()
   {
       $user = factory(User::class)->create(['role' => 'admin']);
       $adminUser = factory(User::class)->create(['role' => 'admin']);
       auth()->login($adminUser);

       $this->assertEquals('admin', $user->role);

       $this->post("/admin/accounts/demote/{$user->id}")->assertStatus(401);
       $this->assertEquals('admin', $user->fresh()->role);
   }

   /** @test */
   function a_superadmin_can_demote_other_users()
   {
       $user = factory(User::class)->create(['role' => 'admin']);
       $adminUser = factory(User::class)->create(['role' => 'superadmin']);
       auth()->login($adminUser);

       $this->assertEquals('admin', $user->role);

       $this->post("/admin/accounts/demote/{$user->id}")->assertStatus(200);
       $this->assertNull($user->fresh()->role);
   }
}
