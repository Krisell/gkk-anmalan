<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignatureRequestField extends Model
{
    use HasFactory;

    protected $guarded = [];

    /** @return BelongsTo<SignatureRequest, $this> */
    public function signature_request(): BelongsTo
    {
        return $this->belongsTo(SignatureRequest::class);
    }

    /** @return BelongsTo<SignatureRequestSigner, $this> */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(SignatureRequestSigner::class, 'signature_request_signer_id');
    }
}
