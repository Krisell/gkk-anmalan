<?php

namespace App;

use App\Models\User;

class SSFLicense
{
    public static function createFor(User $user, $year)
    {
        $isYouth = ((int) $year - $user->birth_year) <= 18;
        $isTwentyFiveOrYounger = ((int) $year - $user->birth_year) <= 25;

        // Correct amounts for 2025, but needs to be updated for 2026.
        $sekAmount = $isYouth ? 200 : 900;

        // Decided on a 300 SEK discount for 2025.
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
