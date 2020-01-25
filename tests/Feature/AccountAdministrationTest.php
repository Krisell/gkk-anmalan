<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountAdministrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_may_se_a_list_of_all_users()
    {
        $user = factory(User::class)->create();
        $adminUser = factory(User::class)->create(['role' => 'admin']);
        auth()->login($adminUser);

        $response = $this->get('/admin/accounts');

        $response->assertOk();
        $response->assertSee($user->email);
        $response->assertSee($adminUser->email);
    }

    /** @test */
    public function a_non_admin_may_not_se_accounts()
    {
        $user = factory(User::class)->create();
        $adminUser = factory(User::class)->create(['role' => 'admin']);

        $this->get('/admin/accounts')->assertRedirect();

        auth()->login($user);

        $response = $this->get('/admin/accounts');

        $response->assertUnauthorized();
        $response->assertDontSee($user->email);
        $response->assertDontSee($adminUser->email);
    }
}
