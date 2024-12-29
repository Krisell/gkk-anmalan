<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\Fortnox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\confirm;

class FortnoxEmailInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fortnox-email-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email invoices in Fortnox';

    /**
     * Execute the console command.
     */
    public function handle(Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            $this->error('Fortnox integration not activated.');

            return;
        }

        // Find all payments that have not been invoiced yet
        $payments = Payment::whereNotNull('fortnox_invoice_document_number')
            ->whereNull('fortnox_invoice_emailed_at')
            ->get();

        $confirmed = confirm(
            label: "{$payments->count()} fakturor kommer skickas ut via mail. Vill du fortsätta?",
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

            $this->emailInvoice($fortnox, $payment);
        }
    }

    private function emailInvoice(Fortnox $fortnox, Payment $payment)
    {
        /** @var \App\Models\User */
        $user = $payment->user;

        Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/invoices/{$payment->fortnox_invoice_document_number}/email");

        $payment->update(['fortnox_invoice_emailed_at' => now()]);

        $this->info("Faktura skickad till {$user->email}.");
    }
}
