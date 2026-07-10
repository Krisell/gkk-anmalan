<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SignatureRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    /** @return HasMany<SignatureRequestSigner, $this> */
    public function signers(): HasMany
    {
        return $this->hasMany(SignatureRequestSigner::class);
    }

    /** @return HasMany<SignatureRequestField, $this> */
    public function fields(): HasMany
    {
        return $this->hasMany(SignatureRequestField::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
