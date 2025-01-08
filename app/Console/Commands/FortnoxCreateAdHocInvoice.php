<?php

namespace App\Console\Commands;

use App\Services\Fortnox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\text;

class FortnoxCreateAdHocInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fortnox:create-ad-hoc-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an ad-hoc invoice in Fortnox';

    /**
     * Execute the console command.
     */
    public function handle(Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            $this->error('Fortnox integration not activated.');

            return;
        }

        $customerName = text('Enter the customer name');
        $articleName = text('Enter the article name');
        $amount = text('Enter the amount');
        $invoiceDate = text('Enter the invoice date (YYYY-MM-DD). This must be before the payment in Fortnox.');

        $response = Http::withToken($fortnox->token())->post(
            'https://api.fortnox.se/3/customers', [
                'Customer' => [
                    'Name' => $customerName,
                    'CustomerNumber' => 'GKK-AD-HOC-'.(int) (\microtime(true) * 1000),
                    'Comments' => 'Created by GKK integration on '.\date('Y-m-d H:i:s'),
                    'Type' => 'PRIVATE',
                ],
            ],
        );

        $customer = $response->json('Customer');

        $response = Http::withToken($fortnox->token())->post(
            'https://api.fortnox.se/3/articles', [
                'Article' => [
                    'Description' => $articleName,
                    'ArticleNumber' => 'GKK-AD-HOC-'.(int) (\microtime(true) * 1000),
                ],
            ],
        );

        $article = $response->json('Article');

        Http::withToken($fortnox->token())->post(
            'https://api.fortnox.se/3/invoices', [
                'Invoice' => [
                    'InvoiceDate' => $invoiceDate,
                    'DueDate' => now()->addDays(30)->format('Y-m-d'),
                    'Comments' => 'Created by GKK integration on '.\date('Y-m-d H:i:s'),
                    'CustomerNumber' => $customer['CustomerNumber'],
                    'OurReference' => 'GKK Kassör',
                    'Country' => 'Sverige',
                    'InvoiceRows' => [
                        [
                            'AccountNumber' => 3013, // Tävlingsintäkter
                            'ArticleNumber' => $article['ArticleNumber'],
                            'Price' => $amount,
                            'DeliveredQuantity' => 1,
                            'VAT' => 0,
                        ],
                    ],
                ],
            ],
        );

        $this->info("Ad-hoc invoice created for {$customerName}. You can now find the invoice in Fortnox and connect a payment to it.");
    }
}
