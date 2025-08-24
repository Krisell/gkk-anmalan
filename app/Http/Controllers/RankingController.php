<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        return view('points-high-score', [
            'view' => 'points-high-score',
        ]);
    }

    public function getPointsRanking(Request $request)
    {
        $year = $request->get('year', \date('Y'));
        $competitionType = $request->get('competition_type', 1);

        $data = Cache::remember("ranking_{$year}_{$competitionType}", now()->addMinutes(10), function () use ($year, $competitionType) {
            $informationRequest = Http::timeout(10)->get('https://data-api.styrkelyft.se/api/results/ranking', [
                'discipline' => 'points',
                'competition_type' => $competitionType,
                'start_date' => $year.'-01-01',
                'page_size' => 1, // Determine count and calculate number of pages with a page_size of 100
            ]);

            $pageSize = 200;
            $pageCount = \ceil($informationRequest->json('page_count') / $pageSize);

            $requestURLs = [];

            for ($i = 1; $i <= $pageCount; $i++) {
                $requestURLs[] = "https://data-api.styrkelyft.se/api/results/ranking?discipline=points&competition_type={$competitionType}&start_date={$year}-01-01&page={$i}&page_size={$pageSize}";
            }

            $response = collect(Http::pool(fn (Pool $pool) => [
                ...\array_map(fn ($url) => $pool->get($url), $requestURLs),
            ]));

            $data = $response->map->json('results')->flatten(1);

            return $data->filter(function ($result) {
                return isset($result['club']['name']) && $result['club']['name'] === 'Göteborg KK';
            });
        });
        // Process and categorize results
        $categorizedResults = $this->categorizeResults($data);

        return response()->json([
            'success' => true,
            'data' => $categorizedResults,
            'year' => $year,
        ]);
    }

    private function categorizeResults($results)
    {
        $categories = [
            'men_ksl' => [],
            'women_ksl' => [],
            'men_kbp' => [],
            'women_kbp' => [],
        ];

        // Group results by person and competition type to get best performance
        $personResults = [];

        foreach ($results as $result) {
            $personId = $result['person']['id'];
            $competitionType = $result['competition_type']['name'];
            $isKSL = $competitionType === 'CPL'; // CPL = Klassisk Styrkelyft
            $isKBP = $competitionType === 'CBP'; // CBP = Klassisk Bänkpress

            if (! $isKSL && ! $isKBP) {
                continue;
            }

            $key = $personId.'_'.$competitionType;

            // Keep the result with the highest points for each person/competition type
            if (! isset($personResults[$key]) || $result['points'] > $personResults[$key]['points']) {
                $personResults[$key] = [
                    'id' => $result['id'],
                    'name' => $result['person']['first_name'].' '.$result['person']['last_name'],
                    'club' => $result['club']['name'],
                    'points' => $result['points'],
                    'is_male' => $result['person']['is_male'],
                    'competition_type' => $competitionType,
                    'weight_class' => $result['weight_class']['name'],
                    'total' => $result['total'],
                    'benchpress' => $result['benchpress'],
                    'body_weight' => $result['body_weight'],
                ];
            }
        }

        // Sort results into categories
        foreach ($personResults as $result) {
            if ($result['competition_type'] === 'CPL') {
                if ($result['is_male']) {
                    $categories['men_ksl'][] = $result;
                } else {
                    $categories['women_ksl'][] = $result;
                }
            } elseif ($result['competition_type'] === 'CBP') {
                if ($result['is_male']) {
                    $categories['men_kbp'][] = $result;
                } else {
                    $categories['women_kbp'][] = $result;
                }
            }
        }

        return $categories;
    }
}
