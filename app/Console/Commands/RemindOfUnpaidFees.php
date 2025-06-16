<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
    protected $description = 'Allows sending reminders to users about unpaid fees.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payments = \App\Models\Payment::whereNull('state')
            ->where('fortnox_invoice_emailed_at', '<=', now()->subDays(30))
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No unpaid fees found.');

            return;
        }

        $this->info('Found '.$payments->count().' unpaid fees.');

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
    }
}
