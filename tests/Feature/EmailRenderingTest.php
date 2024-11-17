<?php

use App\Mail\NotifyAboutNewRegistrationMail;

test('example', function () {
    $mail = new NotifyAboutNewRegistrationMail('test@example.com', 'John Doe', '1992');
    $mail->assertSeeInHTML('En ny medlem har skapat ett konto som har blivit godkänt.');
    $mail->assertSeeInHTML('John Doe');
    $mail->assertSeeInHTML('test@example.com');
    $mail->assertSeeInHTML('Födelseår: 1992');
});
