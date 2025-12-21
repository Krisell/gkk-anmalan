<?php

namespace App\Http\Controllers;

use App\Mail\AccountGrantedMail;
use App\Mail\ExitSurveyMail;
use App\Mail\NotifyAboutNewRegistrationMail;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
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
            $receivers = collect(\explode(',', config('gkk.new-member-receivers')))->map(fn ($email) => \trim($email));

            foreach ($receivers as $receiver) {
                Mail::to($receiver)->send(new NotifyAboutNewRegistrationMail(
                    $user->email,
                    "$user->first_name $user->last_name",
                    $user->birth_year,
                ));
            }
        }
    }

    public function destroyUngranted(User $user)
    {
        if ($user->granted_by == 0) {
            $user->delete();
        }
    }

    public function inactivate(User $user, Request $request)
    {
        $user->update([
            'inactivated_at' => now(),
        ]);

        if ($request->boolean('sendSurveyEmail')) {
            Mail::to($user->email)->send(new ExitSurveyMail($user));
        }
    }

    public function reactivate(User $user)
    {
        $user->update([
            'inactivated_at' => null,
        ]);
    }

    public function updateCompetitionPermission(User $user, Request $request)
    {
        $data = $request->validate([
            'explicit_registration_approval' => 'required|boolean',
        ]);

        $user->update($data);

        ActivityLog::create([
            'performed_by' => auth()->id(),
            'action' => 'explicit-registration-approval-update',
            'user_id' => $user->id,
            'data' => $data['explicit_registration_approval'] ? 'granted' : 'revoked',
        ]);

        return response()->json(['message' => 'Registration approval updated successfully']);
    }
}
