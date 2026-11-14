<?php

namespace App\Services;

use App\Models\Result;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * GKK's competition results from the Swedish Powerlifting Federation's results database (data.styrkelyft.se, whose API
 * is data-api.styrkelyft.se): the last 30 days for the screen slideshow, and further back for importing the club records
 * (gkk:import-club-records).
 *
 * Each lift is marked when it is a club record: a classic lift (CPL, or the bench press in CBP) above the best result in
 * the club's records (App\Models\Result) for the same gender, weight class and event, or that record itself. Equalling
 * someone else's record is not a record.
 */
class StyrkelyftResults
{
    public const API = 'https://data-api.styrkelyft.se/api/results';

    public const CLUB_ID = 7; // Göteborg KK

    public const DAYS = 30;

    // The API gives at most 1 000 results a page; a month in Sweden is usually fewer (see fetch).
    private const PAGE_SIZE = 1000;

    private const WINDOW_DAYS = 31;

    // The most shown on the slide, newest first: 30–40 after a large competition, and more than three rows scroll.
    public const MAX = 60;

    private const CACHE_KEY = 'styrkelyft.results';

    // The last results fetched, used when the API can't be reached, so that a failed request never empties the slide
    // (and changes the slideshow's version, which reloads the screens).
    private const LAST_KEY = 'styrkelyft.results.last';

    private const CACHE_SECONDS = 60 * 60;

    // The club records' weight classes (see Records.vue); results in other classes are never marked.
    public const CLASSES = [
        'M' => ['52', '59', '66', '74', '83', '93', '105', '120', '120+'],
        'F' => ['43', '47', '52', '57', '63', '69', '76', '84', '84+'],
    ];

    public const LIFTS = ['squat' => 'Knäböj', 'bench' => 'Bänkpress', 'deadlift' => 'Marklyft', 'total' => 'Total'];

    /**
     * @return list<array<string, mixed>>
     */
    public static function recent(): array
    {
        $fetched = Cache::get(self::CACHE_KEY);

        if ($fetched === null) {
            try {
                $fetched = self::fetch();
                Cache::put(self::CACHE_KEY, $fetched, self::CACHE_SECONDS);
                Cache::forever(self::LAST_KEY, $fetched);
            } catch (Throwable $e) {
                report($e);
                $fetched = Cache::get(self::LAST_KEY, []);
            }
        }

        // The club records are applied on every build, so that registering a record shows on the slide right away.
        return \array_slice(self::withRecords(self::within($fetched, self::DAYS)), 0, self::MAX);
    }

    /**
     * GKK's results from the API since the given date (the last 30 days by default), normalized, newest first.
     *
     * @return list<array<string, mixed>>
     */
    public static function fetch(?string $since = null): array
    {
        $from = CarbonImmutable::parse($since ?? now()->subDays(self::DAYS)->toDateString())->startOfDay();
        $today = CarbonImmutable::today();
        $results = [];

        // The API has no club filter, so every result in Sweden is fetched: a few hundred a month, and about 60 000
        // since 2015. Its pages have no stable order (results repeat and go missing between pages), so it is asked for
        // at most a month at a time, small enough for one page.
        for ($start = $from; $start <= $today; $start = $start->addDays(self::WINDOW_DAYS)) {
            $end = $start->addDays(self::WINDOW_DAYS - 1)->min($today);
            $results = [...$results, ...self::fetchWindow($start, $end)];
        }

        return collect($results)
            ->unique('id')
            ->filter(fn ($result) => ($result['club']['id'] ?? null) === self::CLUB_ID && ! ($result['disqualified'] ?? false))
            ->map(fn ($result) => self::normalize($result))
            ->filter()
            ->sortByDesc(fn ($result) => $result['date'].' '.$result['id'])
            ->values()
            ->all();
    }

    /**
     * Every result in Sweden between two dates (inclusive), in one page: a window with more results than fit on a page
     * is split in two. A single day with more (never seen) is paged through.
     *
     * @return list<array<string, mixed>>
     */
    private static function fetchWindow(CarbonImmutable $start, CarbonImmutable $end): array
    {
        $response = self::get(self::API, [
            'start_date' => $start->toDateString(),
            // The API's end_date is exclusive: a result on that day is left out.
            'end_date' => $end->addDay()->toDateString(),
            'page_size' => self::PAGE_SIZE,
        ]);
        $results = $response->json('results') ?? [];

        if ($response->json('next') && $start < $end) {
            $middle = $start->addDays(\intdiv((int) $start->diffInDays($end), 2));

            return [...self::fetchWindow($start, $middle), ...self::fetchWindow($middle->addDay(), $end)];
        }

        // The next page's address has the filters in it, so it is fetched as it is.
        for ($url = $response->json('next'), $page = 0; $url && $page < 50; $page++) {
            $response = self::get($url);
            $results = [...$results, ...($response->json('results') ?? [])];
            $url = $response->json('next');
        }

        return $results;
    }

    /**
     * @param  array<string, mixed>|null  $query  null for an address that has its query already (an empty array would
     *                                            remove it)
     */
    private static function get(string $url, ?array $query = null): Response
    {
        return Http::acceptJson()->timeout(30)->retry(2, 1000)->get($url, $query)->throw();
    }

    /**
     * @param  array<string, mixed>  $result
     * @return array<string, mixed>|null
     */
    private static function normalize(array $result): ?array
    {
        $type = $result['competition_type']['name'] ?? null;
        $start = $result['group']['start'] ?? null;
        $person = $result['person'] ?? null;

        if (! $start || ! $person) {
            return null;
        }

        $benchOnly = \in_array($type, ['CBP', 'EBP', 'PBP'], true);
        $number = fn ($value) => \is_numeric($value) && $value > 0 ? (float) $value : null;

        return [
            'id' => $result['id'],
            'date' => \substr($start, 0, 10),
            'competition' => \trim($result['group']['competition']['name'] ?? ''),
            'name' => \trim(($person['first_name'] ?? '').' '.($person['last_name'] ?? '')),
            'first_name' => \trim($person['first_name'] ?? ''),
            'last_name' => \trim($person['last_name'] ?? ''),
            'gender' => ($person['is_male'] ?? $result['weight_class']['is_male'] ?? true) ? 'M' : 'F',
            'type' => $type,
            'type_label' => $result['competition_type']['description'] ?? $type,
            'equipped' => \in_array($type, ['EPL', 'EBP'], true),
            'bench_only' => $benchOnly,
            'age_category' => $result['age_category']['label'] ?? null,
            'weight_class' => self::weightClass($result['weight_class']['name'] ?? ''),
            'body_weight' => $number($result['body_weight'] ?? null),
            'squat' => $benchOnly ? null : $number($result['squat'] ?? null),
            'bench' => $number($result['benchpress'] ?? null),
            'deadlift' => $benchOnly ? null : $number($result['deadlift'] ?? null),
            'total' => $benchOnly ? null : $number($result['total'] ?? null),
            'points' => $number($result['points'][$benchOnly ? 'ipfglbp' : 'ipfgl'] ?? null),
            'rank' => \is_int($result['rank'] ?? null) ? $result['rank'] : null,
        ];
    }

    // "-74" → "74" and "+120" → "120+", as in the club records.
    private static function weightClass(string $name): string
    {
        $name = \trim($name);

        return \str_starts_with($name, '+') ? \substr($name, 1).'+' : \ltrim($name, '-');
    }

    /**
     * @param  list<array<string, mixed>>  $results
     * @return list<array<string, mixed>>
     */
    private static function within(array $results, int $days): array
    {
        $from = now()->subDays($days)->toDateString();

        return \array_values(\array_filter($results, fn ($result) => $result['date'] >= $from));
    }

    /**
     * Marks the club records: each lift that beats the club's record, or is that record (see sameLift). A record beaten
     * more than once in the period is marked on each result that improved it.
     *
     * @param  list<array<string, mixed>>  $results
     * @return list<array<string, mixed>>
     */
    public static function withRecords(array $results): array
    {
        $records = self::clubRecords();
        $best = [];

        // Oldest first, so a later result is compared with an earlier one in the period.
        $marked = collect($results)->reverse()->map(function ($result) use ($records, &$best) {
            $result['records'] = [];

            if ($result['equipped'] || ! \in_array($result['weight_class'], self::CLASSES[$result['gender']] ?? [], true)) {
                return $result;
            }

            foreach (self::LIFTS as $lift => $event) {
                $value = $result[$lift] ?? null;

                if ($value === null || ($result['bench_only'] && $lift !== 'bench')) {
                    continue;
                }

                $key = self::key($result['gender'], $result['weight_class'], $event);
                $record = $records[$key] ?? null;
                $isRecord = match (true) {
                    $record === null => true,
                    $value > $record['value'] => true,
                    $value == $record['value'] => self::sameLift($record, $result),
                    default => false,
                };

                if ($isRecord && $value > ($best[$key] ?? 0)) {
                    $result['records'][] = $lift;
                    $best[$key] = $value;
                }
            }

            return $result;
        });

        return $marked->reverse()->values()->all();
    }

    /**
     * Whether a registered record of the same weight is this result: imported from it, or entered by hand for the same
     * lifter within a few days (a multi-day competition may be registered with another of its days). The same weight
     * by someone else, or by the same lifter at another competition, only equals the record.
     *
     * @param  array{name: string, date: string, styrkelyft_result_id: int|null}  $record
     * @param  array<string, mixed>  $result
     */
    public static function sameLift(array $record, array $result): bool
    {
        if ($record['styrkelyft_result_id'] !== null) {
            return $record['styrkelyft_result_id'] === $result['id'];
        }

        $days = \abs(\strtotime($record['date']) - \strtotime($result['date'])) / 86400;

        return $days <= 3 && self::samePerson($record['name'], $result['name']);
    }

    public static function samePerson(string $a, string $b): bool
    {
        $normalize = fn (string $name) => \preg_replace('/\s+/u', ' ', \mb_strtolower(\trim($name)));

        return $a !== '' && $normalize($a) === $normalize($b);
    }

    /**
     * The value of a club record, which is stored as text ("152.5", or sometimes "152,5").
     */
    public static function value(Result $record): float
    {
        return (float) \str_replace(',', '.', (string) $record->result);
    }

    /**
     * The club's best result for each gender, weight class and event ("M|74|Knäböj").
     *
     * @return array<string, array{id: int, value: float, date: string, name: string, styrkelyft_result_id: int|null}>
     */
    public static function clubRecords(): array
    {
        /** @var array<string, array{id: int, value: float, date: string, name: string, styrkelyft_result_id: int|null}> $records */
        $records = Result::with('user:id,first_name,last_name')
            ->get(['id', 'user_id', 'name', 'gender', 'weight_class', 'event', 'result', 'competition_date', 'styrkelyft_result_id'])
            ->map(fn (Result $result) => [
                'key' => self::key($result->gender, $result->weight_class, $result->event),
                'id' => $result->id,
                'value' => self::value($result),
                'date' => \substr((string) $result->competition_date, 0, 10),
                'name' => \trim($result->name ?: data_get($result, 'user.first_name', '').' '.data_get($result, 'user.last_name', '')),
                'styrkelyft_result_id' => $result->styrkelyft_result_id === null ? null : (int) $result->styrkelyft_result_id,
            ])
            ->groupBy('key')
            ->map(fn ($group) => $group->sortByDesc('value')->first())
            ->all();

        return $records;
    }

    /**
     * A lifter's best registered classic results (CPL, and the bench press also from CBP), by license number, for the
     * live control: whether the attempt on the platform would be a personal best. Null when the federation has no one
     * with that license number. Today's competition isn't registered yet, so it is never counted.
     *
     * @return array{name: string, competitions: int, bests: array<string, array{value: float, date: string, competition: string}|null>}|null
     */
    public static function personalBests(string $license): ?array
    {
        return Cache::remember('styrkelyft.personal-bests.'.\md5(\mb_strtolower($license)), 6 * 60 * 60, function () use ($license) {
            $person = self::get('https://data-api.styrkelyft.se/api/people', ['license_id' => $license])->json('results.0');

            if (! \is_array($person) || ! isset($person['id'])) {
                return null;
            }

            $results = collect(self::get(self::API, ['person' => $person['id'], 'page_size' => self::PAGE_SIZE])->json('results') ?? [])
                ->filter(fn ($result) => \is_array($result) && ! ($result['disqualified'] ?? false))
                ->map(fn ($result) => self::normalize($result))
                ->filter(fn ($result) => $result !== null && \in_array($result['type'], ['CPL', 'CBP'], true));

            $bests = [];

            foreach (\array_keys(self::LIFTS) as $lift) {
                $best = $results
                    ->filter(fn ($result) => $result[$lift] !== null)
                    // The heaviest, and the first time it was lifted.
                    ->sortBy([[$lift, 'desc'], ['date', 'asc']])
                    ->first();
                $bests[$lift] = $best === null ? null : [
                    'value' => $best[$lift],
                    'date' => $best['date'],
                    'competition' => $best['competition'],
                ];
            }

            return [
                'name' => \trim(($person['first_name'] ?? '').' '.($person['last_name'] ?? '')),
                'competitions' => $results->count(),
                'bests' => $bests,
            ];
        });
    }

    public static function key(string $gender, string $weightClass, string $event): string
    {
        return $gender.'|'.$weightClass.'|'.$event;
    }
}
