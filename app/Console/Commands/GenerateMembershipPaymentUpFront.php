<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class GenerateMembershipPaymentUpFront extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-membership-payment-up-front';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = select(
            label: 'What year?',
            options: [(string) (now()->year - 1), (string) (now()->year), (string) (now()->year + 1)],
        );

        $users = User::all();

        $count = 0;

        foreach ($users as $user) {
            if ($user->payments()->where('type', 'MEMBERSHIP')->where('year', $year)->exists()) {
                continue;
            }

            if ($user->inactivated_at) {
                continue;
            }

            $count++;

            $discountedByAge = ((int) $year - $user->birth_year) <= 23 || ((int) $year - $user->birth_year) >= 65;

            $user->payments()->create([
                'type' => 'MEMBERSHIP',
                'year' => $year,
                'sek_amount' => $discountedByAge ? 700 : 1500,
                'modifier' => $discountedByAge ? 'AGE_DISCOUNT' : null,
            ]);
        }

        $this->info("{$count} membership payments created successfully.");
    }
}
