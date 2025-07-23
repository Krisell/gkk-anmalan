<?php

use App\Mail\ExitSurveyMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('an_inactive_user_is_redirected_to_an_information_page', function () {
    login(User::factory()->inactivated()->create());

    $this->get('/')->assertRedirect('/inactivated');
    $this->get('/events')->assertRedirect('/inactivated');
    $this->get('/competitions')->assertRedirect('/inactivated');
    $this->get('/dokument')->assertRedirect('/inactivated');
});

test('an_administrator_can_inactivate_a_user_account', function () {
    $user = User::factory()->create();

    $this->assertNull($user->inactivated_at);

    login(User::factory()->create(['role' => 'user']));

    $this->post("/admin/accounts/inactivate/{$user->id}")->assertUnauthorized();

    loginAdmin();

    $this->post("/admin/accounts/inactivate/{$user->id}")->assertOk();
    $this->assertNotNull($user->fresh()->inactivated_at);
});

test('an_administrator_can_reactivate_a_user_account', function () {
    $user = User::factory()->inactivated()->create();
    $this->assertNotNull($user->inactivated_at);

    login(User::factory()->create(['role' => 'user']));

    $this->post("/admin/accounts/reactivate/{$user->id}")->assertUnauthorized();

    loginAdmin();

    $this->post("/admin/accounts/reactivate/{$user->id}")->assertOk();
    $this->assertNull($user->fresh()->inactivated_at);
});

test('exit_survey_email_is_sent_when_sendSurveyEmail_is_true', function () {
    Mail::fake();

    $user = User::factory()->create();
    loginAdmin();

    $this->post("/admin/accounts/inactivate/{$user->id}", [
        'sendSurveyEmail' => true,
    ])->assertOk();

    Mail::assertSent(ExitSurveyMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && $mail->user->id === $user->id;
    });
});

test('exit_survey_email_is_not_sent_when_sendSurveyEmail_is_false', function () {
    Mail::fake();

    $user = User::factory()->create();
    loginAdmin();

    $this->post("/admin/accounts/inactivate/{$user->id}", [
        'sendSurveyEmail' => false,
    ])->assertOk();

    Mail::assertNotSent(ExitSurveyMail::class);
});

test('exit_survey_email_is_not_sent_when_sendSurveyEmail_is_not_provided', function () {
    Mail::fake();

    $user = User::factory()->create();
    loginAdmin();

    $this->post("/admin/accounts/inactivate/{$user->id}")->assertOk();

    Mail::assertNotSent(ExitSurveyMail::class);
});

test('exit_survey_email_contains_correct_user_data', function () {
    Mail::fake();

    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);
    loginAdmin();

    $this->post("/admin/accounts/inactivate/{$user->id}", [
        'sendSurveyEmail' => true,
    ])->assertOk();

    Mail::assertSent(ExitSurveyMail::class, function ($mail) use ($user) {
        return $mail->hasTo('john.doe@example.com')
            && $mail->user->first_name === 'John'
            && $mail->user->last_name === 'Doe'
            && $mail->user->id === $user->id;
    });
});
