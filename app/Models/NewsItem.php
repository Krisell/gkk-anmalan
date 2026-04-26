<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function emailSends(): MorphMany
    {
        return $this->morphMany(EmailSend::class, 'subject');
    }
}
