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

    public function registrations()
    {
        return $this->hasMany(CompetitionRegistration::class);
    }
}
