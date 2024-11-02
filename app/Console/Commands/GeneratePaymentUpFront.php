<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class GeneratePaymentUpFront extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-payment-up-front';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = select(
            label: 'What type?',
            options: ['MEMBERSHIP'],
        );

        $year = select(
            label: 'What year?',
            options: [(string) (now()->year - 1), (string) (now()->year), (string) (now()->year + 1)],
        );

        $users = User::all();

        $count = 0;
        foreach ($users as $user) {
            if ($user->payments()->where('type', $type)->where('year', $year)->exists()) {
                continue;
            }

            $count++;

            $user->payments()->create([
                'type' => $type,
                'year' => $year,
                // The following calucaltion is only correct for MEMBERSHIP fees.
                'sek_amount' => (int) $year - $user->birth_year <= 23 ? 700 : 1500,
            ]);
        }

        $this->info("{$count} payments created successfully.");
    }
}
