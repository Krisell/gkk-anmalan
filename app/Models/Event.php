<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'publish_count' => 'boolean',
        'publish_list' => 'boolean',
        'deleted_at' => 'datetime',
        'last_registration_at' => 'datetime:Y-m-d',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function getPublishCountValueAttribute()
    {
        return $this->registrations()->whereStatus(1)->count();
    }

    public function getPublishListValueAttribute()
    {
        return $this->registrations()->whereStatus(1)->with('user:id,first_name,last_name')->get();
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