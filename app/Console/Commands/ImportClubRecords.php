<?php

namespace App\Console\Commands;

use App\Mail\ClubRecordsImported;
use App\Models\Result;
use App\Models\User;
use App\Services\StyrkelyftResults;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Adds the club records set by GKK's lifters to the club records (App\Models\Result), from their results on
 * data.styrkelyft.se, and emails what was added. Runs nightly over the last 14 days, since results are sometimes entered
 * or corrected a few days after a competition. Run it with --since and --dry-run to compare the whole history with the
 * club records first.
 *
 * Only the best new record per gender, weight class and event is added (see StyrkelyftResults for what counts). A record
 * entered by hand for the same lift is linked to its result instead of added again, and an imported one is never
 * imported twice. Records are never changed or removed: if a result is corrected or disqualified later, the record
 * has to be deleted by hand in the admin.
 */
class ImportClubRecords extends Command
{
    protected $signature = 'gkk:import-club-records
        {--since= : The first competition date to look at (YYYY-MM-DD); by default 14 days ago}
        {--dry-run : Only show what would be added or linked, without saving or emailing}';

    protected $description = 'Add new club records from the results on data.styrkelyft.se';

    public const DAYS = 14;

    public const NOTIFY = 'martin.krisell@gmail.com';

    public function handle(): int
    {
        $since = $this->option('since');

        if ($since !== null && ! \preg_match('/^\d{4}-\d{2}-\d{2}$/', $since)) {
            $this->error('--since must be a date: YYYY-MM-DD');

            return self::FAILURE;
        }
        $since ??= now()->subDays(self::DAYS)->toDateString();
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Hämtar GKK:s resultat sedan {$since} från data.styrkelyft.se …");
        $results = StyrkelyftResults::fetch($since);
        $this->line(\count($results).' resultat.');

        $changes = $this->changes(StyrkelyftResults::withRecords($results));

        if ($changes === []) {
            $this->info('Inga nya klubbrekord.');

            return self::SUCCESS;
        }

        $this->table(
            ['', 'Datum', 'Namn', 'Klass', 'Gren', 'Resultat', 'Nuvarande rekord', 'Tävling'],
            \array_map(fn ($change) => [
                $change['action'] === 'add' ? 'Lägg till' : 'Koppla',
                $change['date'],
                $change['name'].($change['user_id'] ? '' : ' (ej medlem i systemet)'),
                ($change['gender'] === 'F' ? 'Damer ' : 'Herrar ').$change['weight_class'].' kg',
                $change['event'],
                $change['result'].' kg',
                $change['previous'],
                $change['competition'],
            ], $changes),
        );

        $added = \array_values(\array_filter($changes, fn ($change) => $change['action'] === 'add'));
        $linked = \count($changes) - \count($added);
        $summary = \count($added).' nya klubbrekord, '.$linked.' redan registrerade att koppla till sina resultat.';

        if ($dryRun) {
            $this->warn($summary.' Inget sparades (--dry-run).');

            return self::SUCCESS;
        }

        foreach ($changes as $change) {
            if ($change['action'] === 'link') {
                Result::whereKey($change['record_id'])->update(['styrkelyft_result_id' => $change['styrkelyft_result_id']]);
            } else {
                Result::create([
                    'gender' => $change['gender'],
                    'competition_date' => $change['date'],
                    'weight_class' => $change['weight_class'],
                    'event' => $change['event'],
                    'user_id' => $change['user_id'],
                    // Shown from the member when there is one, as for the records entered in the admin.
                    'name' => $change['user_id'] ? null : $change['name'],
                    'result' => $change['result'],
                    'styrkelyft_result_id' => $change['styrkelyft_result_id'],
                ]);
            }
        }

        if ($added !== []) {
            Mail::to(self::NOTIFY)->send(new ClubRecordsImported($added));
        }

        $this->info($summary);

        return self::SUCCESS;
    }

    /**
     * The best new record per gender, weight class and event (the earliest, when equal), as a record to add, or to link
     * when it is already registered by hand. Oldest first.
     *
     * @param  list<array<string, mixed>>  $results  with their records marked (StyrkelyftResults::withRecords)
     * @return list<array<string, mixed>>
     */
    private function changes(array $results): array
    {
        $records = StyrkelyftResults::clubRecords();
        $best = [];

        foreach ($results as $result) {
            foreach ($result['records'] as $lift) {
                $key = StyrkelyftResults::key($result['gender'], $result['weight_class'], StyrkelyftResults::LIFTS[$lift]);
                $value = $result[$lift];
                $current = $best[$key] ?? null;

                if ($current === null || $value > $current['value'] || ($value == $current['value'] && $result['date'] < $current['result']['date'])) {
                    $best[$key] = ['value' => $value, 'lift' => $lift, 'result' => $result];
                }
            }
        }

        $changes = [];

        foreach ($best as $key => ['value' => $value, 'lift' => $lift, 'result' => $result]) {
            $record = $records[$key] ?? null;
            $registered = $record !== null && $value == $record['value'] && StyrkelyftResults::sameLift($record, $result);

            if ($registered && $record['styrkelyft_result_id'] !== null) {
                continue; // Imported already.
            }

            $changes[] = [
                'action' => $registered ? 'link' : 'add',
                'record_id' => $registered ? $record['id'] : null,
                'styrkelyft_result_id' => $result['id'],
                'date' => $result['date'],
                'competition' => $result['competition'],
                'name' => $result['name'],
                'user_id' => $this->member($result['first_name'], $result['last_name']),
                'gender' => $result['gender'],
                'weight_class' => $result['weight_class'],
                'event' => StyrkelyftResults::LIFTS[$lift],
                'result' => self::format($value),
                'previous' => $record && ! $registered
                    ? self::format($record['value']).' kg, '.$record['name'].', '.Carbon::parse($record['date'])->toDateString()
                    : ($registered ? 'Detta resultat' : '–'),
            ];
        }

        // By date and lifter, and the lifts in their order.
        $order = \array_flip(\array_values(StyrkelyftResults::LIFTS));
        \usort($changes, fn ($a, $b) => [$a['date'], $a['name'], $order[$a['event']]] <=> [$b['date'], $b['name'], $order[$b['event']]]);

        return $changes;
    }

    /**
     * The member with exactly this name, if there is exactly one.
     */
    private function member(string $firstName, string $lastName): ?int
    {
        if ($firstName === '' || $lastName === '') {
            return null;
        }

        $ids = User::whereRaw('LOWER(first_name) = ?', [\mb_strtolower($firstName)])
            ->whereRaw('LOWER(last_name) = ?', [\mb_strtolower($lastName)])
            ->limit(2)
            ->pluck('id');

        return $ids->count() === 1 ? $ids->first() : null;
    }

    // 152.5 → "152.5", 340.0 → "340", as the records entered in the admin.
    private static function format(float $value): string
    {
        return \rtrim(\rtrim(\number_format($value, 2, '.', ''), '0'), '.');
    }
}
