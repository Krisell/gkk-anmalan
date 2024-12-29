<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payment $payment): bool
    {
        if (collect(['admin', 'superadmin'])->contains($user->role)) {
            return true;
        }

        return $user->id === $payment->user_id;
    }

    public function show(User $user, Payment $payment): bool
    {
        return $this->update($user, $payment);
    }
}
