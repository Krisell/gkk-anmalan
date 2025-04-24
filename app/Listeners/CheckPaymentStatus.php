<?php

namespace App\Listeners;

use App\Events\UserWasActiveEvent;

class CheckPaymentStatus
{
    public function handle(UserWasActiveEvent $event): void
    {
        $user = $event->user;

        $pendingPayments = $user->payments()
            ->whereNull('state')
            ->where('year', now()->year)
            ->where('created_at', '<=', now()->subDays(3))
            ->get();

        session(['has_pending_payment' => $pendingPayments->isNotEmpty()]);
        logger(session('has_pending_payment'));
    }
}
