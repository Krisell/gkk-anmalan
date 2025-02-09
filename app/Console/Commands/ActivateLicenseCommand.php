<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class ActivateLicenseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkk:activate-license';

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
        $user = $this->getSingleUser()->first();

        $year = select(
            label: 'Välj år för avgift',
            options: [(string) (now()->year + 1), (string) (now()->year), (string) (now()->year - 1)],
        );

        if ($user->payments()->where('type', 'SSFLICENSE')->where('year', $year)->exists()) {
            $this->info('User already has an active license for this year.');
            exit;
        }

        $isYouth = ((int) $year - $user->birth_year) <= 18;
        $isTwentyFiveOrYounger = ((int) $year - $user->birth_year) <= 25;

        // Correct amounts for 2025, but needs to be updated for 2026.
        $sekAmount = $isYouth ? 200 : 900;

        // Decided on a 300 SEK discount for 2025.
        $sekDiscount = $isTwentyFiveOrYounger ? 300 : 0;

        $user->payments()->create([
            'type' => 'SSFLICENSE',
            'year' => $year,
            'sek_amount' => $sekAmount,
            'sek_discount' => $sekDiscount,
            'modifier' => $isYouth ? 'YOUTH' : null,
            'state' => $sekDiscount >= $sekAmount ? 'PAID' : null,
        ]);

        $this->info('License activated successfully.');
    }

    private function getSingleUser()
    {
        $search = $this->ask('Sök efter användare');

        $users = User::where('email', 'like', "%{$search}%")->get();

        if ($users->count() === 0) {
            $this->error('No users found.');
            exit;
        }

        $email = select(
            label: 'Välj användare',
            options: $users->map(fn ($user) => $user->email)->toArray(),
        );

        return $users->where('email', $email);
    }
}
