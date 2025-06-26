<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Fortnox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;
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

        $customerNumber = $this->getCustomerNumber($fortnox);
        $accountNumber = text(
            'Enter the account number for the invoice (e.g. 3013 for Tävlingsintäkter).',
            default: '3013',
        );

        $articleName = text('Enter the article name');
        $amount = text('Enter the amount');
        $invoiceDate = text(
            'Enter the invoice date (YYYY-MM-DD). In case the payment has already been made, you must set this date to a few days before the payment date.',
            default: now()->subDays(5)->format('Y-m-d'),
        );

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
                    'CustomerNumber' => $customerNumber,
                    'OurReference' => 'GKK Kassör',
                    'Country' => 'Sverige',
                    'InvoiceRows' => [
                        [
                            'AccountNumber' => $accountNumber,
                            'ArticleNumber' => $article['ArticleNumber'],
                            'Price' => $amount,
                            'DeliveredQuantity' => 1,
                            'VAT' => 0,
                        ],
                    ],
                ],
            ],
        );

        $this->info('Ad-hoc invoice created. You can now find the invoice in Fortnox and connect a payment to it.');
    }

    private function getCustomerNumber(Fortnox $fortnox): string
    {
        $chooseUser = confirm(
            'Do you want to select a GKK member as the receiver of the invoice?',
            false,
        );

        if ($chooseUser) {
            $allUsers = User::all()
                ->mapWithKeys(fn (User $user) => [$user->id => "{$user->first_name} {$user->last_name}"])
                ->toArray();

            $userId = search(
                label: 'Which GKK member do you want to create the invoice for?',
                options: fn (string $value) => \strlen($value) > 0
                    ? \array_filter($allUsers, fn (string $option) => \str_contains(\strtolower($option), \strtolower($value)))
                    : [],
            );

            // Assume the user has been synced to Fortnox and has a customer number
            return User::find($userId)->fortnox_customer_id;
        }

        $customerName = text('Enter the customer name');

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

        return $response->json('Customer')['CustomerNumber'];
    }
}
