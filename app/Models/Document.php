<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

    /** @return BelongsTo<DocumentFolder, $this> */
    public function document_folder(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class);
    }
}
