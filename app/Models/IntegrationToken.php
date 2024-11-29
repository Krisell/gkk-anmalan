<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationToken extends Model
{
    protected $guarded = [];

    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'access_token_expires_at' => 'datetime',
    ];
}
