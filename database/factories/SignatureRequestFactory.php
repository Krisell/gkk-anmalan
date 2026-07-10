<?php

namespace Database\Factories;

use App\Models\SignatureRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SignatureRequestFactory extends Factory
{
    protected $model = SignatureRequest::class;

    public function definition()
    {
        return [
            'name' => 'Årsmötesprotokoll',
            'pdf_url' => 'https://URL.com/document.pdf',
            'created_by' => User::factory(),
        ];
    }
}
