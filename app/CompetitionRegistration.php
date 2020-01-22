<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompetitionRegistration extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
