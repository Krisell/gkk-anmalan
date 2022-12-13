<?php

namespace App\Http\Controllers;

use App\MusicHelpSet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use NumberFormatter;

class MusikhjalpenController extends Controller
{
    public function index()
    {
        $donatedAmount = Cache::remember('musikhjalpen-donated-amount', 10, function () {
            $response = Http::get('https://bossan.musikhjalpen.se/server/fundraisers/5pHB9pLTRDStX1bRplY95q?fields[]=amount&fields=prev_amount');

            return $response->json()['amount'] ?? '';
        });

        $formatter = new NumberFormatter('sv_SE', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $liftedWeight = MusicHelpSet::all()->map(function ($set) {
            return $set->weight_lifted * $set->repetitions;
        })->sum();

        return view('musikhjalpen', [
            'site' => 'musikhjalpen',
            'donatedAmount' => $formatter->formatCurrency($donatedAmount, 'SEK'),
            'donatedAmountRaw' => $donatedAmount,
            'liftedWeight' => $liftedWeight,
        ]);
    }
}
