<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Str;

class ImpersonationController extends Controller
{
    public function store($emailOrId)
    {
        abort_if(session()->has('original_impersonating_user'), 403, 'Avsluta pågående impersonering först.');

        $user = \is_numeric($emailOrId)
            ? User::whereId($emailOrId)->firstOrFail()
            : User::where(['email' => Str::lower($emailOrId)])->firstOrFail();

        $admin = auth()->user();

        abort_if($user->is($admin), 422, 'Du kan inte impersonera dig själv.');
        abort_if($user->role === 'superadmin' && $admin->role !== 'superadmin', 403);

        ActivityLog::create([
            'performed_by' => $admin->id,
            'action' => 'impersonation',
            'user_id' => $user->id,
        ]);

        $this->switchTo($user);

        session(['original_impersonating_user' => $admin->id]);
    }

    public function delete()
    {
        abort_unless(session()->has('original_impersonating_user'), 403);

        $admin = User::findOrFail(session('original_impersonating_user'));

        $this->switchTo($admin);
    }

    /**
     * Start from an empty session so nothing stored for one user (flash messages,
     * cached flags, intended URLs) leaks to the other.
     */
    private function switchTo(User $user): void
    {
        session()->invalidate();
        session()->regenerateToken();

        auth()->login($user);
    }
}
