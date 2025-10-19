<?php

namespace App\Mail;

use App\Models\Competition;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompetitionDeadlineNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $competition;

    public $daysUntilDeadline;

    public $deadlineType;

    /**
     * Create a new message instance.
     *
     * @param  string  $deadlineType  ('5_days_before', 'last_day', 'day_after')
     */
    public function __construct(Competition $competition, int $daysUntilDeadline, string $deadlineType)
    {
        $this->competition = $competition;
        $this->daysUntilDeadline = $daysUntilDeadline;
        $this->deadlineType = $deadlineType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subjectMap = [
            '5_days_before' => 'Påminnelse: 5 dagar kvar till anmälningsslut',
            'last_day' => 'Sista dagen för anmälan',
            'day_after' => 'Anmälningsperiod avslutad - dags att skicka in anmälan',
        ];

        $subject = $subjectMap[$this->deadlineType].' - '.$this->competition->name;

        return $this->subject($subject)
            ->markdown('emails.competition-deadline-notification');
    }
}
