<?php

namespace Tests\Feature;

use App\Mail\GrantAccountMail;
use App\Mail\WelcomeMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AccountRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_email_is_sent_to_the_registered_user()
    {
        $this->post('register')->assertSessionHasErrors();

        Mail::fake();
        $this->post('register', [
            'first_name' => 'Martin',
            'last_name' => 'Krisell',
            'email' => 'martin.krisell@gmail.com',
            'password' => 'asdasdasd',
            'password_confirmation' => 'asdasdasd',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'martin.krisell@gmail.com',
        ]);

        Mail::assertSent(WelcomeMail::class, function ($mail) {
            return $mail->hasTo('martin.krisell@gmail.com');
        });
    }

    /** @test */
    public function an_email_is_sent_to_administrators_abount_account_granting()
    {
        Mail::fake();
        $this->post('register', [
            'first_name' => 'Martin',
            'last_name' => 'Krisell',
            'email' => 'kurt.svensson@gmail.com',
            'password' => 'asdasdasd',
            'password_confirmation' => 'asdasdasd',
        ])->assertRedirect('/');

        Mail::assertSent(GrantAccountMail::class, function ($mail) {
            return $mail->hasTo('martin.krisell@gmail.com'); // administrator
        });
    }
}
