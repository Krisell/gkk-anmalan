<?php

namespace App\Models;

use Database\Factories\SlideFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    /** @use HasFactory<SlideFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'json',
        'is_visible' => 'boolean',
    ];
}
