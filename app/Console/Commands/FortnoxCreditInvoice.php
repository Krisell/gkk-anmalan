<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\User;
use App\Services\Fortnox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

class FortnoxCreditInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fortnox:credit-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credit an invoice in Fortnox';

    /**
     * Execute the console command.
     */
    public function handle(Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            $this->error('Fortnox integration not activated.');

            return;
        }

        $allUsers = User::all()
            ->mapWithKeys(fn (User $user) => [$user->id => "{$user->first_name} {$user->last_name}"])
            ->toArray();

        $userId = search(
            label: 'Which user do you want to credit an invoice for?',
            options: fn (string $value) => \strlen($value) > 0
                ? \array_filter($allUsers, fn (string $option) => \str_contains(\strtolower($option), \strtolower($value)))
                : [],
        );

        $payments = Payment::query()
            ->where('user_id', $userId)
            ->whereNotNull('fortnox_invoice_document_number')
            ->get();

        $fortnoxInvoiceDocumentNumber = select(
            label: 'Välj faktura att kreditera',
            options: $payments->mapWithKeys(fn (Payment $payment) => [$payment->fortnox_invoice_document_number => "Fakturanummer {$payment->fortnox_invoice_document_number}, {$payment->sek_amount} kr ({$payment->year})"])->toArray(),
        );

        Http::withToken($fortnox->token())
            ->put("https://api.fortnox.se/3/invoices/{$fortnoxInvoiceDocumentNumber}/credit")
            ->throw();

        $this->info("Invoice {$fortnoxInvoiceDocumentNumber} credited.");
    }
}
