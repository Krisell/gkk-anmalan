<?php

namespace App\Services;

use App\Models\Result;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * GKK's competition results from the last 30 days, from the Swedish Powerlifting Federation's results database
 * (data.styrkelyft.se, whose API is data-api.styrkelyft.se), for the screen slideshow.
 *
 * Each lift is marked when it is a club record: a classic lift (CPL, or the bench press in CBP) at or above the best
 * result in the club's records (App\Models\Result) for the same gender, weight class and event.
 */
class StyrkelyftResults
{
    public const API = 'https://data-api.styrkelyft.se/api/results';

    public const CLUB_ID = 7; // Göteborg KK

    public const DAYS = 30;

    // The most shown on the slide, newest first: 30–40 after a large competition, and more than three rows scroll.
    public const MAX = 60;

    private const CACHE_KEY = 'styrkelyft.results';

    // The last results fetched, used when the API can't be reached, so that a failed request never empties the slide
    // (and changes the slideshow's version, which reloads the screens).
    private const LAST_KEY = 'styrkelyft.results.last';

    private const CACHE_SECONDS = 60 * 60;

    // The club records' weight classes (see Records.vue); results in other classes are never marked.
    private const CLASSES = [
        'M' => ['52', '59', '66', '74', '83', '93', '105', '120', '120+'],
        'F' => ['43', '47', '52', '57', '63', '69', '76', '84', '84+'],
    ];

    private const LIFTS = ['squat' => 'Knäböj', 'bench' => 'Bänkpress', 'deadlift' => 'Marklyft', 'total' => 'Total'];

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
        return self::withRecords(self::within($fetched, self::DAYS));
    }

    /**
     * GKK's results from the API, normalized, newest first.
     *
     * @return list<array<string, mixed>>
     */
    public static function fetch(): array
    {
        $results = [];
        $url = self::API;
        $query = ['start_date' => now()->subDays(self::DAYS)->toDateString(), 'page_size' => 1000];

        // Only a few hundred results a month in the whole country, so one page; the rest are followed just in case.
        for ($page = 0; $url && $page < 10; $page++) {
            $response = Http::acceptJson()->timeout(15)->get($url, $query)->throw();
            $results = [...$results, ...($response->json('results') ?? [])];
            $url = $response->json('next');
            $query = [];
        }

        return collect($results)
            ->filter(fn ($result) => ($result['club']['id'] ?? null) === self::CLUB_ID && ! ($result['disqualified'] ?? false))
            ->map(fn ($result) => self::normalize($result))
            ->filter()
            ->sortByDesc(fn ($result) => $result['date'].' '.$result['id'])
            ->values()
            ->all();
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
     * Marks the club records: each lift that beats the club's record, or is the record once it has been registered (the
     * same weight, by the same lifter or within a few days). A record beaten more than once in the period is marked on
     * each result that improved it.
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

                $key = $result['gender'].'|'.$result['weight_class'].'|'.$event;
                $record = $records->get($key);
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

        return $marked->reverse()->take(self::MAX)->values()->all();
    }

    /**
     * Whether a registered record of the same weight is this result.
     *
     * @param  array{name: string, date: string}  $record
     * @param  array<string, mixed>  $result
     */
    private static function sameLift(array $record, array $result): bool
    {
        $days = \abs(\strtotime($record['date']) - \strtotime($result['date'])) / 86400;

        return $days <= 3 || \mb_strtolower($record['name']) === \mb_strtolower($result['name']);
    }

    /**
     * The club's best result for each gender, weight class and event.
     *
     * @return Collection<string, array{value: float, date: string, name: string}>
     */
    private static function clubRecords(): Collection
    {
        /** @var Collection<string, array{value: float, date: string, name: string}> */
        return Result::with('user:id,first_name,last_name')
            ->get(['id', 'user_id', 'name', 'gender', 'weight_class', 'event', 'result', 'competition_date'])
            ->map(fn (Result $result) => [
                'key' => $result->gender.'|'.$result->weight_class.'|'.$result->event,
                'value' => (float) \str_replace(',', '.', $result->result),
                'date' => \substr((string) $result->competition_date, 0, 10),
                'name' => \trim($result->name ?: data_get($result, 'user.first_name', '').' '.data_get($result, 'user.last_name', '')),
            ])
            ->groupBy('key')
            ->map(fn ($group) => $group->sortByDesc('value')->first());
    }
}
