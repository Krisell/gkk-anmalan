<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * The club records added by gkk:import-club-records, so that someone checks them.
 */
class ClubRecordsImported extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  list<array<string, mixed>>  $records
     */
    public function __construct(public array $records) {}

    public function build()
    {
        $count = \count($this->records);

        return $this->subject($count === 1 ? 'Nytt klubbrekord registrerat' : "{$count} nya klubbrekord registrerade")
            ->markdown('emails.club-records-imported');
    }
}
