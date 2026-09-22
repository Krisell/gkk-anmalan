<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionAdminTask extends Model
{
    use HasFactory;

    public const SUBMIT_REGISTRATION = 'submit_registration';

    public const PAY_REGISTRATION_FEE = 'pay_registration_fee';

    protected $guarded = [];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /** @return BelongsTo<Competition, $this> */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
