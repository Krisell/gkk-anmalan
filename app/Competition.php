<?php

namespace App;

use App\CompetitionRegistration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guarded = [];
    protected $casts = [
        'publish_count' => 'boolean',
        'publish_list' => 'boolean',
        'last_registration_at' => 'datetime:Y-m-d',
        'date' => 'datetime:Y-m-d',
    ];

    public function registrations()
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->format('Y-m-d'))->orWhere('date', null);
    }
}
