<?php

use App\Mail\GrantAccountMail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

test('an email is sent to the registered user', function () {
    $this->post('register')->assertSessionHasErrors();

    Mail::fake();
    $this->post('register', [
        'first_name' => 'Martin',
        'last_name' => 'Krisell',
        'email' => 'martin.krisell@gmail.com',
        'password' => 'asdasdasd',
        'password_confirmation' => 'asdasdasd',
    ])->assertRedirect('/insidan');

    $this->assertDatabaseHas('users', [
        'email' => 'martin.krisell@gmail.com',
    ]);

    Mail::assertSent(WelcomeMail::class, function ($mail) {
        return $mail->hasTo('martin.krisell@gmail.com');
    });
});

test('an email is sent to administrators abount account granting', function () {
    Mail::fake();

    $this->post('register', [
        'first_name' => 'Martin',
        'last_name' => 'Krisell',
        'email' => 'kurt.svensson@gmail.com',
        'password' => 'asdasdasd',
        'password_confirmation' => 'asdasdasd',
    ])->assertRedirect('/insidan');

    Mail::assertSent(GrantAccountMail::class, function ($mail) {
        return $mail->hasTo('martin.krisell@gmail.com'); // administrator
    });
});
