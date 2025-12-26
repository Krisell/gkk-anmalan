<?php

namespace App;

use App\Models\User;

class SSFLicense
{
    public static function createFor(User $user, $year)
    {
        $isYouth = ((int) $year - $user->birth_year) <= 18;
        $isTwentyFiveOrYounger = ((int) $year - $user->birth_year) <= 25;

        // Amounts for 2026, may need to be update for 2027 and beyond.
        $sekAmount = $isYouth ? 300 : 1050;

        // Decided on a 300 SEK discount for 2025 - preliminarily enabled for 2026 as well.
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
}
