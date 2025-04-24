<?php

namespace App\Listeners;

use App\Events\UserWasActiveEvent;

class LogUserActivity
{
    public function handle(UserWasActiveEvent $event): void
    {
        $user = $event->user;

        $user->update([
            'visits' => $user->visits + 1,
            'last_visited_at' => now(),
        ]);
    }
}
