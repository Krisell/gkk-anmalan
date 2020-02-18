<?php

namespace App\Console\Commands;

use App\SignUpLink;
use Illuminate\Console\Command;
use App\Mail\CreateRegistrationMail;
use Illuminate\Support\Facades\Mail;

class SendRegistrationEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkk:send-registration-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $receivers = [
            ['email' => 'martin.krisell@gmail.com', 'firstName' => 'Martin', 'lastName' => 'Krisell'],
        ];

        foreach ($receivers as $receiver) {
            Mail::to($receiver['email'])->send(new CreateRegistrationMail(
                app(SignUpLink::class)->make($receiver['email'], $receiver['firstName'], $receiver['lastName'])
            ));

            $this->info("Send email to {$receiver['email']}");
        }
    }
}
