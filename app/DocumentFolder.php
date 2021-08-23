<?php

namespace App;

use App\Document;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFolder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
