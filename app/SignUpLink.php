<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SignUpLink extends Model
{
    public function make($email, $firstName, $lastName)
    {
        $data = base64_encode(json_encode([
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
        ]));

        return "https://".request()->getHost()."/register?data=$data";
    }
}
