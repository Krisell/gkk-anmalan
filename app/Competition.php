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
        'last_registration_at' => 'date',
    ];

    public function registrations()
    {
        return $this->hasMany(CompetitionRegistration::class);
    }
}
