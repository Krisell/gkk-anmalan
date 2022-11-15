<?php

namespace App\Http\Controllers;

use App\Payment;
use App\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, User $user)
    {
        return Payment::updateOrCreate([
            'user_id' => $user->id,
            'type' => $request->input('type'),
            'year' => $request->input('year'),
        ]);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
    }
}
