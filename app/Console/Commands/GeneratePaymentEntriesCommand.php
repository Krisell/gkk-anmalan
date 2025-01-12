<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

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

        if ($type === 'SSFLICENSE') {
            $this->error('SSFLICENSE handling is not implemented yet.');
            exit;
        }

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

            if ($user->is_honorary_member && $type === 'MEMBERSHIP') {
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
