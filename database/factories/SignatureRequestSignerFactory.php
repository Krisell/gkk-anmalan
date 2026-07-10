<?php

namespace Database\Factories;

use App\Models\SignatureRequest;
use App\Models\SignatureRequestSigner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SignatureRequestSignerFactory extends Factory
{
    protected $model = SignatureRequestSigner::class;

    public function definition()
    {
        return [
            'signature_request_id' => SignatureRequest::factory(),
            'user_id' => User::factory(),
        ];
    }
}
