<?php

namespace App\Console\Commands;

use App\SignUpLink;
use Illuminate\Console\Command;
use App\Mail\CreateRegistrationMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompetitionInformationMail;

class SendRegistrationEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkk:send-registration-emails {--confirm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send registration emails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $receivers = json_decode(file_get_contents(base_path('recipients.json')), true);

        if (!$this->option('confirm')) {
            foreach ($receivers as $receiver) {
                $this->info("Will send email to {$receiver['email']}");
            }

            return;
        }

        foreach ($receivers as $receiver) {
            Mail::to($receiver['email'])->send(new CompetitionInformationMail(
                app(SignUpLink::class)->make($receiver['email'], $receiver['firstName'], $receiver['lastName'])
            ));

            $this->info("Sent email to {$receiver['email']}");
        }
    }
}
