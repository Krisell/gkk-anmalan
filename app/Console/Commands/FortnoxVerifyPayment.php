<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\Fortnox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class FortnoxVerifyPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fortnox:verify-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify payments in Fortnox';

    /**
     * Execute the console command.
     */
    public function handle(Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            $this->error('Fortnox integration not activated.');

            return;
        }

        $target = select(
            label: 'Vill du kontrollera alla betalningar eller enbart senaste 10?',
            options: [
                'LATEST_10' => 'Senaste 10',
                'ALL' => 'Alla',
            ],
        );

        $paymentsQuery = Payment::query()
            ->whereNotNull('fortnox_invoice_document_number')
            ->whereNull('state');

        if ($target === 'LATEST_10') {
            $paymentsQuery->latest()->take(10);
        }

        $payments = $paymentsQuery->get();

        $confirmed = confirm(
            label: "{$payments->count()} fakturor är inte markerade som betalda. Vill du kontrollera dessa mot Fortnox?",
            default: false,
            yes: 'Ja',
            no: 'Nej',
        );

        if (! $confirmed) {
            return;
        }

        foreach ($payments as $index => $payment) {
            if ($index > 0 && $index % 5 === 0) {
                \sleep(2);
            }

            $this->verifyPayment($fortnox, $payment);
        }
    }

    private function verifyPayment(Fortnox $fortnox, Payment $payment)
    {
        /** @var \App\Models\User */
        $user = $payment->user;

        $response = Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/invoices/{$payment->fortnox_invoice_document_number}");

        if ($response->json('Invoice.FinalPayDate') !== null) {
            $payment->update(['state' => 'PAID']);

            $this->info("Faktura {$payment->fortnox_invoice_document_number} är markerad som betald.");
        }
    }
}
