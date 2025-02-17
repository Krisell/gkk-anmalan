<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Fortnox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;

class AdjustStudentStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkk:adjust-student-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update student status for a user.';

    /**
     * Execute the console command.
     */
    public function handle(Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            $this->error('Fortnox integration not activated.');

            return;
        }

        $allUsers = User::all()
            ->mapWithKeys(fn (User $user) => [$user->id => "{$user->first_name} {$user->last_name}"])
            ->toArray();

        $userId = search(
            label: 'Which user do you want to adjust the student status for?',
            options: fn (string $value) => \strlen($value) > 0
                ? \array_filter($allUsers, fn (string $option) => \str_contains(\strtolower($option), \strtolower($value)))
                : [],
        );

        $user = User::find($userId);

        if ($user->is_student_over_23) {
            $this->info("{$user->first_name} {$user->last_name} currently has a student status.");
            $confirmed = confirm('Are you sure you want to remove the student status?');

            if (! $confirmed) {
                $this->info('Student status not removed.');

                return;
            }

            $user->update(['is_student_over_23' => false]);
        } else {
            $this->info("{$user->first_name} {$user->last_name} currently does not have a student status.");
            $confirmed = confirm('Are you sure you want to set the student status?');

            if (! $confirmed) {
                $this->info('Student status not set.');

                return;
            }

            $user->update(['is_student_over_23' => true]);
        }

        /** @var ?\App\Models\Payment $payment */
        $payment = $user->payments()->whereNotNull('fortnox_invoice_document_number')->first();

        if (! $payment) {
            $this->info('User does not have a MEMBERSHIP invoice.');

            return;
        }

        if ($payment->state !== 'PAID') {
            $this->info('User has an unpaid MEMBERSHIP invoice. Trying to credit the invoice.');

            Http::withToken($fortnox->token())
                ->put("https://api.fortnox.se/3/invoices/{$payment->fortnox_invoice_document_number}/credit")
                ->throw();

            $this->info('Invoice credited.');
        }

        $payment->delete();

        $this->info('Payment entry deleted.');
        $this->info('You can now generate a new payment entry for the user, and then create new invoice and send it.');
    }
}
