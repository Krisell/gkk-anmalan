<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_superadmin_may_load_dev_routes()
    {
        $this->json('get', '/admin/dev/phpinfo')->assertUnauthorized();
        $this->json('get', '/admin/dev/opcache')->assertUnauthorized();

        $user = User::factory()->create();
        auth()->login($user);

        $this->json('get', '/admin/dev/phpinfo')->assertUnauthorized();
        $this->json('get', '/admin/dev/opcache')->assertUnauthorized();

        $adminUser = User::factory()->create(['role' => 'admin']);
        auth()->login($adminUser);

        $this->json('get', '/admin/dev/phpinfo')->assertUnauthorized();
        $this->json('get', '/admin/dev/opcache')->assertUnauthorized();

        $superadminUser = User::factory()->create(['role' => 'superadmin']);
        auth()->login($superadminUser);

        \ob_start();
        $this->json('get', '/admin/dev/phpinfo')->assertOk();
        $this->json('get', '/admin/dev/opcache')->assertOk();
        \ob_end_clean();
    }
}
