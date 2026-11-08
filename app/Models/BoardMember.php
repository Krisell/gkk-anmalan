<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** A member of the board, as shown on the Om GKK page. */
class BoardMember extends Model
{
    protected $guarded = [];

    protected $attributes = [
        'areas' => '[]',
    ];

    protected function casts(): array
    {
        return [
            'areas' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /** The initials shown in place of a photo, e.g. "AA" for Anna Andersson. */
    public function initials(): string
    {
        $parts = \preg_split('/\s+/', \trim($this->name));

        return \mb_strtoupper(\mb_substr($parts[0], 0, 1).(\count($parts) > 1 ? \mb_substr(\end($parts), 0, 1) : ''));
    }
}
