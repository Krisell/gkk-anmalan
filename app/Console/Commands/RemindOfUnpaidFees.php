<?php

namespace App\Console\Commands;

use App\Mail\UnpaidFeeReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;

class RemindOfUnpaidFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkk:remind-of-unpaid-fees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows sending reminders to users about unpaid fees for membership or licenses.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $paymentType = select(
            'Which payment type do you want to check?',
            [
                'MEMBERSHIP' => 'Membership fees',
                'SSFLICENSE' => 'SSF License fees',
                'COMPETITION' => 'Competition fees',
                'OTHER' => 'Other fees',
                'ALL' => 'All fees',
            ],
            'ALL',
        );

        $query = \App\Models\Payment::whereNull('state')
            ->where('fortnox_invoice_emailed_at', '<=', now()->subDays(30));

        if ($paymentType !== 'ALL') {
            $query->where('type', $paymentType);
        }

        $payments = $query->get();

        if ($payments->isEmpty()) {
            $this->info('No unpaid fees found for '.($paymentType === 'ALL' ? 'all payment types' : $paymentType).'.');

            return;
        }

        $this->info('Found '.$payments->count().' unpaid '.($paymentType === 'ALL' ? '' : \strtolower($paymentType).' ').'fees.');

        table(
            headers: ['User', 'Payment Type', 'Year', 'Amount'],
            rows: $payments->map(function ($payment) {
                /** @var \App\Models\User $user */
                $user = $payment->user;

                return [
                    'User' => $user->first_name.' '.$user->last_name,
                    'Payment Type' => $payment->type,
                    'Year' => $payment->year,
                    'Amount' => $payment->sek_amount,
                ];
            })->toArray(),
        );

        $confirmed = confirm(
            label: "Vill du skicka påminnelser via email till {$payments->count()} användare?",
            default: false,
            yes: 'Ja',
            no: 'Nej',
        );

        if (! $confirmed) {
            $this->info('Inga emails skickade.');

            return;
        }

        $this->info('Skickar påminnelser...');

        $sentCount = 0;

        foreach ($payments as $payment) {
            /** @var \App\Models\User $user */
            $user = $payment->user;

            Mail::to($user->email)->send(new UnpaidFeeReminderMail($user, $payment));
            $sentCount++;
            $this->line("✓ Påminnelse skickad till {$user->first_name} {$user->last_name} ({$user->email})");
        }

        $this->info("Totalt {$sentCount} påminnelser skickade.");
    }
}
