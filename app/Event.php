<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }
}
