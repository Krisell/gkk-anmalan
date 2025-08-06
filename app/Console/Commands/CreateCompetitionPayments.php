<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\Payment;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreateCompetitionPayments extends Command
{
    protected $signature = 'payments:create-competition';

    protected $description = 'Create competition payment entries for registered users';

    public function handle()
    {
        $competitions = Competition::visible()
            ->orderBy('date', 'desc')
            ->get();

        if ($competitions->isEmpty()) {
            $this->error('No competitions found.');

            return;
        }

        $competitionOptions = $competitions->mapWithKeys(function ($competition) {
            $label = $competition->name;

            if ($competition->date) {
                $label .= ' ('.$competition->date->format('Y-m-d').')';
            }

            return [$competition->id => $label];
        })->toArray();

        $competitionId = select(
            label: 'Välj tävling',
            options: $competitionOptions,
        );

        $competition = $competitions->find($competitionId);

        $seniorAmount = text(
            label: 'Avgift för seniorer (SEK)',
            validate: fn (string $value) => \is_numeric($value) && $value > 0 ? null : 'Ange ett giltigt belopp',
        );

        $juniorAmount = text(
            label: 'Avgift för juniorer (SEK)',
            validate: fn (string $value) => \is_numeric($value) && $value > 0 ? null : 'Ange ett giltigt belopp',
        );

        $currentYear = now()->year;
        $juniorCutoffYear = $currentYear - 23;

        $registrations = CompetitionRegistration::where('competition_id', $competitionId)
            ->where('status', 1)
            ->with('user')
            ->get();

        if ($registrations->isEmpty()) {
            $this->error("Inga registrerade deltagare hittades för {$competition->name}.");

            return;
        }

        $existingPayments = Payment::where('type', 'COMPETITION')
            ->where('year', $currentYear)
            ->whereIn('user_id', $registrations->pluck('user.id'))
            ->whereHas('user.competitionRegistrations', function ($query) use ($competitionId) {
                $query->where('competition_id', $competitionId);
            })
            ->count();

        $newPaymentsCount = $registrations->count() - $existingPayments;

        if ($newPaymentsCount <= 0) {
            $this->info("Alla deltagare har redan betalningsuppgifter för {$competition->name}.");

            return;
        }

        $this->info("Tävling: {$competition->name}");
        $this->info("Senior avgift: {$seniorAmount} SEK");
        $this->info("Junior avgift: {$juniorAmount} SEK");
        $this->info("Antal nya betalningar: {$newPaymentsCount}");

        $confirmed = confirm(
            label: "Skapa {$newPaymentsCount} nya betalningsuppgifter?",
            default: false,
            yes: 'Ja',
            no: 'Nej',
        );

        if (! $confirmed) {
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($registrations as $registration) {
            /** @var \App\Models\User $user */
            $user = $registration->user;

            $existingPayment = Payment::where('type', 'COMPETITION')
                ->where('year', $currentYear)
                ->where('user_id', $user->id)
                ->whereHas('user.competitionRegistrations', function ($query) use ($competitionId) {
                    $query->where('competition_id', $competitionId);
                })
                ->exists();

            if ($existingPayment) {
                $skipped++;

                continue;
            }

            $isJunior = $user->birth_year && $user->birth_year > $juniorCutoffYear;
            $amount = $isJunior ? (int) $juniorAmount : (int) $seniorAmount;

            Payment::create([
                'user_id' => $user->id,
                'type' => 'COMPETITION',
                'year' => $currentYear,
                'sek_amount' => $amount,
                'sek_discount' => 0,
                'state' => null,
                'modifier' => null,
            ]);

            $ageLabel = $isJunior ? 'junior' : 'senior';
            $this->info("Skapade betalning för {$user->first_name} {$user->last_name} ({$ageLabel}, {$amount} SEK)");

            $created++;
        }

        $this->info("\nSummering:");
        $this->info("Skapade: {$created} betalningar");
        $this->info("Hoppade över: {$skipped} betalningar");
    }
}
