<?php

namespace App;

use App\DocumentFolder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function document_folder()
    {
        return $this->belongsTo(DocumentFolder::class);
    }
}
