<?php

use App\Mail\AccountGrantedMail;
use App\User;
use Illuminate\Support\Facades\Mail;

test('a not granted user can not view any page except for nagivation', function () {
    login(User::factory()->ungranted()->create());

    $this->get('/competitions')->assertRedirect('/insidan');
    $this->get('/events')->assertRedirect('/insidan');
    $this->get('/member-documents')->assertRedirect('/insidan');

    auth()->user()->update(['granted_by' => 1]);

    $this->get('/competitions')->assertOk();
    $this->get('/events')->assertOk();
    $this->get('/member-documents')->assertOk();
});

test('an admin can grant a new account which triggers an email', function () {
    $user = User::factory()->ungranted()->create();

    loginAdmin();

    Mail::fake();
    $this->post("/admin/accounts/{$user->id}/grant")->assertOk();

    $this->assertEquals(auth()->id(), $user->fresh()->granted_by);
    Mail::assertSent(AccountGrantedMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('an admin can delete an ungranted account', function () {
    $user = User::factory()->ungranted()->create();

    loginAdmin();

    $this->delete("/admin/accounts/{$user->id}/grant")->assertOk();

    $this->assertNull($user->fresh());
});

test('an admin cannot delete a granted account', function () {
    $user = User::factory()->create();

    loginAdmin();

    $this->delete("/admin/accounts/{$user->id}/grant")->assertOk();

    $this->assertNotNull($user->fresh());
});

test('a non admin cant grant a new account or delete a user', function () {
    $user = User::factory()->ungranted()->create();

    login();

    $this->post("/admin/accounts/{$user->id}/grant")->assertUnauthorized();
    $this->delete("/admin/accounts/{$user->id}/grant")->assertUnauthorized();

    $this->assertEquals(0, $user->fresh()->granted_by);
});
