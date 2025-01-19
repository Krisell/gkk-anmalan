<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class GeneratePaymentEntriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkk:generate-payment-entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = select(
            label: 'Välj år för avgift',
            options: [(string) (now()->year + 1), (string) (now()->year), (string) (now()->year - 1)],
        );

        $type = select(
            label: 'Välj typ av avgift',
            options: [
                'MEMBERSHIP' => 'Medlemsavgift',
                'SSFLICENSE' => 'Tävlingslicens',
            ],
        );

        $handlers = [
            'MEMBERSHIP' => 'generateMembershipPayments',
            'SSFLICENSE' => 'generateLicensePayments',
        ];

        if (! \array_key_exists($type, $handlers)) {
            $this->error('Invalid payment type.');
            exit;
        }

        $this->{$handlers[$type]}($year);
    }

    private function generateLicensePayments($year)
    {
        // Find users registered to competitions in the selected year but without a generated payment entry.
        $yearCompetitions = Competition::whereBetween('date', ["{$year}-01-01", "{$year}-12-31"])->get();
        $registeredUsers = $yearCompetitions->flatMap(function ($competition) {
            return $competition->registrations->where('status', true)->pluck('user');
        })->unique();
        $missingLicensesUsers = $registeredUsers->filter(fn ($user) => ! $user->payments()->where('type', 'SSFLICENSE')->where('year', $year)->exists());

        $confirmed = confirm("{$missingLicensesUsers->count()} license payments will be created. Do you want to continue?");

        if (! $confirmed) {
            return;
        }

        foreach ($missingLicensesUsers as $user) {
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
        }

        $this->info("{$missingLicensesUsers->count()} license payments created successfully.");
    }

    private function generateMembershipPayments($year)
    {
        $target = select(
            label: 'Välj målgrupp',
            options: [
                'SINGLE' => 'Enskild medlem',
                'MULTIPLE' => 'Flera på en gång',
            ],
        );

        $users = $target === 'SINGLE' ? $this->getSingleUser() : $this->getMultipleUsers();

        $count = 0;

        foreach ($users as $user) {
            if ($user->payments()->where('type', 'MEMBERSHIP')->where('year', $year)->exists()) {
                continue;
            }

            if ($user->is_honorary_member) {
                $this->info("{$user->email} is an honorary member so no payment was created.");

                continue;
            }

            if ($user->inactivated_at) {
                continue;
            }

            $count++;

            $discountedByAge = ((int) $year - $user->birth_year) <= 23 || ((int) $year - $user->birth_year) >= 65;
            $discountedByStudentState = $user->is_student_over_23;

            $sekAmount = ($discountedByAge || $discountedByStudentState) ? 700 : 1500;

            $user->payments()->create([
                'type' => 'MEMBERSHIP',
                'year' => $year,
                'sek_amount' => $sekAmount,
                'sek_discount' => 0, // Age discounts are handled as separate articles. This field is used for half year memberships.
                'modifier' => $discountedByAge ? 'AGE_DISCOUNT' : ($discountedByStudentState ? 'STUDENT_DISCOUNT' : null),
            ]);
        }

        $this->info("{$count} membership payments created successfully.");
    }

    private function getMultipleUsers()
    {
        $take = $this->ask('Hur många användare vill du hämta?');

        return User::where('inactivated_at', null)->whereNotExists(function ($query) {
            $query->select('id')
                ->from('payments')
                ->whereColumn('payments.user_id', 'users.id')
                ->where('payments.type', 'MEMBERSHIP')
                ->where('payments.year', now()->year);
        })->take($take)->get();
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
