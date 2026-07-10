<?php

namespace Database\Factories;

use App\Models\SignatureRequest;
use App\Models\SignatureRequestField;
use App\Models\SignatureRequestSigner;
use Illuminate\Database\Eloquent\Factories\Factory;

class SignatureRequestFieldFactory extends Factory
{
    protected $model = SignatureRequestField::class;

    public function definition()
    {
        return [
            'signature_request_id' => SignatureRequest::factory(),
            'signature_request_signer_id' => SignatureRequestSigner::factory(),
            'page_index' => 0,
            'x' => 100,
            'y' => 100,
            'width' => 150,
            'height' => 50,
        ];
    }
}
