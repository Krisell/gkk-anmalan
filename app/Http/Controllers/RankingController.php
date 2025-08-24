<?php

namespace App\Http\Controllers;

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
            return Http::timeout(30)->get('https://europe-north1-goteborg-kraftsportklubb.cloudfunctions.net/rankings', [
                'competition_type' => $competitionType,
                'year' => $year,
            ])->json();
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
