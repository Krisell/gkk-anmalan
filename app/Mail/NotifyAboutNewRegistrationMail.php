<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyAboutNewRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public $email, public $name, public $birthYear) {}

    public function build()
    {
        return $this->subject('Ny medlem har skapat GKK-konto')->markdown('emails.new-registration');
    }
}
