<?php

use App\Mail\AccountGrantedMail;
use App\Mail\NotifyAboutNewRegistrationMail;
use App\Models\User;
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

test('an admin can grant a new account which triggers an email to the user', function () {
    $user = User::factory()->ungranted()->create();

    loginAdmin();

    Mail::fake();
    $this->post("/admin/accounts/{$user->id}/grant")->assertOk();

    $this->assertEquals(auth()->id(), $user->fresh()->granted_by);
    Mail::assertSent(AccountGrantedMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('no additional notifications are sent by default', function () {
    $user = User::factory()->ungranted()->create();

    loginAdmin();

    Mail::fake();
    $this->post("/admin/accounts/{$user->id}/grant")->assertOk();

    Mail::assertNotSent(NotifyAboutNewRegistrationMail::class);
});

test('additional notifications can be sent if configured', function () {
    $user = User::factory()->ungranted()->create();

    loginAdmin();

    config(['gkk.new-member-receivers' => 'user1@example.com, user2@example.com ']);

    Mail::fake();
    $this->post("/admin/accounts/{$user->id}/grant")->assertOk();

    Mail::assertSent(NotifyAboutNewRegistrationMail::class, function ($mail) {
        return $mail->hasTo('user1@example.com');
    });

    Mail::assertSent(NotifyAboutNewRegistrationMail::class, function ($mail) {
        return $mail->hasTo('user2@example.com');
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
