<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_update_their_name()
    {
        $user = User::factory()->create([
            'first_name' => 'Before1',
            'last_name' => 'Before2',
        ]);

        auth()->login($user);

        $this->post('/profile/name', [
            'first_name' => 'After1',
            'last_name' => 'After2',
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'first_name' => 'After1',
            'last_name' => 'After2',
        ]);
    }

    /** @test */
    public function a_user_can_update_their_email()
    {
        $user = User::factory()->create(['email' => 'a@b.c']);

        auth()->login($user);

        $this->post('/profile/email', ['email' => 'b@c.d'])->assertOk();

        $this->assertDatabaseHas('users', ['email' => 'b@c.d']);
    }

    /** @test */
    public function a_user_can_update_their_password()
    {
        $user = User::factory()->create(['password' => bcrypt('old_password')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'WRONG',
        ]);

        $this->assertNull(auth()->id());

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'old_password',
        ]);

        $this->assertEquals($user->id, auth()->id());

        $this->json('post', '/profile/password', [
            'password' => 'new_password',
            'password_confirmation' => 'not_confirmed',
        ])->assertStatus(422);

        $this->post('/profile/password', [
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ])->assertOk();

        $this->post('/logout');
        $this->assertNull(auth()->id());

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'old_password',
        ]);
        $this->assertNull(auth()->id());

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'new_password',
        ]);

        $this->assertEquals($user->id, auth()->id());
    }
}
