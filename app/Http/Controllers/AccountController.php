<?php

namespace App\Http\Controllers;

use App\Mail\AccountGrantedMail;
use App\Mail\NotifyAboutNewRegistrationMail;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AccountController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            return User::with(['eventRegistrations.event', 'payments'])->get();
        }

        return view('admin.accounts', [
            'ungranted' => User::whereGrantedBy(0)->get(),
            'accounts' => User::with(['eventRegistrations.event', 'payments'])->get(),
            'user' => auth()->user(),
            'view' => 'accounts',
        ]);
    }

    public function promote(User $user)
    {
        $user->update(['role' => 'admin']);

        ActivityLog::create([
            'performed_by' => auth()->id(),
            'action' => 'account-promotion',
            'user_id' => $user->id,
        ]);
    }

    public function demote(User $user)
    {
        $user->update(['role' => null]);

        ActivityLog::create([
            'performed_by' => auth()->id(),
            'action' => 'account-demotion',
            'user_id' => $user->id,
        ]);
    }

    public function grant(User $user)
    {
        $user->update(['granted_by' => auth()->id()]);

        Mail::to($user->email)->send(new AccountGrantedMail);

        if (config('gkk.new-member-receivers')) {
            $receivers = collect(explode(',', config('gkk.new-member-receivers')))->map(fn ($email) => trim($email));

            foreach ($receivers as $receiver) {
                Mail::to($receiver)->send(new NotifyAboutNewRegistrationMail(
                    $user->email,
                    "$user->first_name $user->last_name")
                );
            }
        }
    }

    public function destroyUngranted(User $user)
    {
        if ($user->granted_by == 0) {
            $user->delete();
        }
    }

    public function inactivate(User $user)
    {
        $user->update([
            'inactivated_at' => now(),
        ]);
    }

    public function reactivate(User $user)
    {
        $user->update([
            'inactivated_at' => null,
        ]);
    }
}
