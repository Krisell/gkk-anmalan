<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\Fortnox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class FortnoxCreateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fortnox:create-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create invoices in Fortnox';

    /**
     * Execute the console command.
     */
    public function handle(Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            $this->error('Fortnox integration not activated.');

            return;
        }

        $type = select(
            label: 'Välj vilken avgift som ska skapas som fakturor i Fortnox',
            options: ['MEMBERSHIP', 'SSFLICENSE'],
        );

        $description = match ($type) {
            'MEMBERSHIP' => 'Medlemsavgift',
            'SSFLICENSE' => 'Tävlingslicens',
            default => throw new \Exception('Invalid type'),
        };

        $year = 2025;

        $this->ensureArticleExists(
            fortnox: $fortnox,
            articleNumber: "GKK-{$type}-{$year}",
            description: "{$description} {$year}",
        );

        $this->ensureArticleExists(
            fortnox: $fortnox,
            articleNumber: "GKK-{$type}-{$year}-DISCOUNT",
            description: "{$description} {$year}, rabatterad",
        );

        $payments = Payment::query()
            ->where('year', $year)
            ->whereNull('fortnox_invoice_document_number')
            ->whereNull('state')
            ->whereType($type)
            ->get();

        $confirmed = confirm(
            label: "{$payments->count()} betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?",
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

            $this->createInvoice($fortnox, $payment);
        }
    }

    private function createInvoice(Fortnox $fortnox, Payment $payment)
    {
        /** @var \App\Models\User */
        $user = $payment->user;

        $articleNumber = "GKK-{$payment->type}-{$payment->year}";

        if ($payment->modifier === 'AGE_DISCOUNT' || $payment->modifier === 'STUDENT_DISCOUNT' || $payment->modifier === 'YOUTH') {
            $articleNumber .= '-DISCOUNT';
        }

        $response = Http::withToken($fortnox->token())->post(
            'https://api.fortnox.se/3/invoices', [
                'Invoice' => [
                    'DueDate' => now()->addDays(30)->format('Y-m-d'),
                    'Comments' => 'Created by GKK integration',
                    'CustomerNumber' => $user->fortnox_customer_id,
                    'OurReference' => 'GKK Kassör',
                    'Country' => 'Sverige',
                    'InvoiceRows' => [
                        [
                            'ArticleNumber' => $articleNumber,
                            'Price' => $payment->sek_amount,
                            'Discount' => $payment->sek_discount,
                            'DiscountType' => 'AMOUNT',
                            'DeliveredQuantity' => 1,
                            'VAT' => 0,
                        ],
                    ],
                ],
            ],
        );

        $invoice = $response->json()['Invoice'];

        $payment->update([
            'fortnox_invoice_document_number' => $invoice['DocumentNumber'],
            'fortnox_invoice_created_at' => now(),
        ]);

        $this->info("Faktura {$invoice['DocumentNumber']} skapad för {$user->first_name} {$user->last_name}.");
    }

    // Load existing article or create one. This ensures all invoices use the same article, simplifying bookkeeping.
    private function ensureArticleExists(Fortnox $fortnox, string $articleNumber, string $description)
    {
        $response = Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/articles/{$articleNumber}");

        if ($response->status() === 200) {
            $this->info("Artikeln {$articleNumber} finns i Fortnox.");

            return;
        }

        $confirmed = confirm(
            label: "Artikeln {$articleNumber} finns inte i Fortnox. Vill du skapa den?",
            default: false,
            yes: 'Ja',
            no: 'Nej',
        );

        if (! $confirmed) {
            return;
        }

        $response = Http::withToken($fortnox->token())->post(
            'https://api.fortnox.se/3/articles', [
                'Article' => [
                    'ArticleNumber' => $articleNumber,
                    'Description' => $description,
                ],
            ],
        );
    }
}
