<?php

namespace App\Http\Controllers;

use App\AuditLog;
use App\Payment;
use App\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, User $user)
    {
        $payment = Payment::updateOrCreate([
            'user_id' => $user->id,
            'type' => $request->input('type'),
            'year' => $request->input('year'),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => "created payment {$payment->id}",
        ]);

        return $payment;
    }

    public function destroy(Payment $payment)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => "deleted payment {$payment->id}",
        ]);

        $payment->delete();
    }
}
