<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    protected $casts = [
        'publish_count' => 'boolean',
        'publish_list' => 'boolean',
        'last_registration_at' => 'datetime:Y-m-d',
        'date' => 'datetime:Y-m-d',
        'end_date' => 'datetime:Y-m-d',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('show_status', 'show')->orWhere(function ($query) {
            $query->where('show_status', '!=', 'hide')->where(function ($query) {
                $query->where('date', '>=', now()->format('Y-m-d'))->orWhere('date', null);
            });
        });
    }
}
