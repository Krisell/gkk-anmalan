<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Mail\AccountGrantedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GrantNewUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_not_granted_user_can_not_view_any_page_except_for_nagivation()
    {
        auth()->login(User::factory()->ungranted()->create());

        $this->get('/competitions')->assertRedirect('/');
        $this->get('/events')->assertRedirect('/');
        $this->get('/documents')->assertRedirect('/');

        auth()->user()->update(['granted_by' => 1]);

        $this->get('/competitions')->assertOk();
        $this->get('/events')->assertOk();
        $this->get('/documents')->assertOk();
    }

    /** @test */
    public function an_admin_can_grant_a_new_account_which_triggers_an_email()
    {
        $user = User::factory()->ungranted()->create();

        auth()->login(User::factory()->create(['role' => 'admin']));

        Mail::fake();
        $this->post("/admin/accounts/{$user->id}/grant")->assertOk();

        $this->assertEquals(auth()->id(), $user->fresh()->granted_by);
        Mail::assertSent(AccountGrantedMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /** @test */
    public function an_admin_can_delete_an_ungranted_account()
    {
        $user = User::factory()->ungranted()->create();

        auth()->login(User::factory()->create(['role' => 'admin']));

        $this->delete("/admin/accounts/{$user->id}/grant")->assertOk();

        $this->assertNull($user->fresh());
    }

    /** @test */
    public function an_admin_cannot_delete_a_granted_account()
    {
        $user = User::factory()->create();

        auth()->login(User::factory()->create(['role' => 'admin']));

        $this->delete("/admin/accounts/{$user->id}/grant")->assertOk();

        $this->assertNotNull($user->fresh());
    }

    /** @test */
    public function a_non_admin_cant_grant_a_new_account_or_delete_a_user()
    {
        $user = User::factory()->ungranted()->create();

        auth()->login(User::factory()->create());

        $this->post("/admin/accounts/{$user->id}/grant")->assertUnauthorized();
        $this->delete("/admin/accounts/{$user->id}/grant")->assertUnauthorized();

        $this->assertEquals(0, $user->fresh()->granted_by);
    }
}
