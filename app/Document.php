<?php

namespace App;

use App\DocumentFolder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function document_folder()
    {
        return $this->belongsTo(DocumentFolder::class);
    }
}
