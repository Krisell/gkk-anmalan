<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SignatureRequestSigner extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function (self $signer) {
            $signer->token ??= (string) Str::uuid();
        });
    }

    /** @return BelongsTo<SignatureRequest, $this> */
    public function signature_request(): BelongsTo
    {
        return $this->belongsTo(SignatureRequest::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<SignatureRequestField, $this> */
    public function fields(): HasMany
    {
        return $this->hasMany(SignatureRequestField::class);
    }
}
