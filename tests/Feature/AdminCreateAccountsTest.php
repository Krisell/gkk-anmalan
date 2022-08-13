<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCreateAccountsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_administrator_can_create_accounts()
    {
        $user = User::factory()->create(['role' => 'admin']);
        auth()->login($user);

        $this->withoutExceptionHandling();
        $this->assertCount(1, User::all());

        $this->post('/admin/accounts', [
            'accounts' => [
                ['firstName' => 'Martin', 'lastName' => 'Krisell', 'email' => 'martin@example.com'],
                ['firstName' => 'Nils', 'lastName' => 'Krisell', 'email' => 'nils@example.com'],
            ],
        ]);

        // $this->assertCount(3, User::all());
    }
}
