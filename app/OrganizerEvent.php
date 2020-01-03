<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizerEvent extends Model
{
    protected $guarded = [];

    public function registrations()
    {
        return $this->hasMany(OrganizerEventRegistration::class);
    }
}
