<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnpaidFeeReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $payment;

    public $typeText;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Payment $payment)
    {
        $this->user = $user;
        $this->payment = $payment;

        $map = [
            'COMPETITION' => 'Tävlingsavgift',
            'SSF_LICENSE' => 'SSF-licens',
            'MEMBERSHIP' => 'Medlemsavgift',
        ];

        $this->typeText = $map[$payment->type] ?? $payment->type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Påminnelse om obetald avgift')
            ->markdown('emails.unpaid-fee-reminder');
    }
}
