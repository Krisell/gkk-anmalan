<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Fortnox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\confirm;

class SyncFortnoxCustomersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-fortnox-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync customer ids from Fortnox.';

    /**
     * Execute the console command.
     */
    public function handle(Fortnox $fortnox)
    {
        $currentCustomers = Http::withToken($fortnox->token())->get('https://api.fortnox.se/3/customers');

        $confirmed = confirm(
            'Found '.\count($currentCustomers->json()['Customers']).' customers in Fortnox. Do you want to continue?',
            default: false,
        );

        if (! $confirmed) {
            return;
        }

        $users = User::whereNull('inactivated_at')->whereNull('fortnox_customer_id')->get();

        $confirmed = confirm(
            'Found '.\count($users).' active users without Fortnox connection in the database. Do you want to sync these?',
            default: false,
        );

        if (! $confirmed) {
            return;
        }

        foreach ($users as $index => $user) {
            if ($index > 0 && $index % 5 === 0) {
                $this->info('Sleeping for 2 seconds ...');
                \sleep(2);
            }

            $this->info("Syncing user $index");

            $todaysDate = \date('Y-m-d');
            $response = Http::withToken($fortnox->token())->post('https://api.fortnox.se/3/customers', [
                'Customer' => [
                    'CustomerNumber' => "GKK-{$todaysDate}-{$user->id}",
                    'Name' => "$user->first_name $user->last_name",
                    'Email' => $user->email,
                    'Comments' => 'Created by GKK integration on '.\date('Y-m-d H:i:s'),
                ],
            ]);

            if ($response->failed()) {
                $this->error("Failed to sync user $index");

                continue;
            }

            $user->update([
                'fortnox_customer_id' => $response->json()['Customer']['CustomerNumber'],
            ]);
        }
    }
}
