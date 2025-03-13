<?php

namespace App\Console\Commands;

use App\Models\User;
use App\SSFLicense;
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

        SSFLicense::createFor($user, $year);

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
