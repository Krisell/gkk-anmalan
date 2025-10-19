<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventCompetitionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $item;

    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item, $type)
    {
        $this->item = $item;
        $this->type = $type; // 'event' or 'competition'
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->type === 'event'
            ? 'Ny funktionärsanmälan: '.$this->item->name
            : 'Ny tävlingsanmälan: '.$this->item->name;

        return $this->subject($subject)
            ->markdown('emails.event-competition-notification');
    }
}
