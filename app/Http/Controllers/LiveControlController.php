<?php

namespace App\Http\Controllers;

use App\Firebase;
use Illuminate\Http\Request;

/**
 * Remote control of the live overlay, e.g. from an iPad: pause slates, messages, PB stamps and so on.
 *
 * There is one broadcast at a time. The control page and the overlay talk through Firebase's Realtime Database
 * (live/). Anyone can read there (it is what the broadcast shows anyway); writing needs a Firebase sign-in, which
 * this controller hands out in exchange for the PIN (LIVE_CONTROL_PIN, only on the server). No account is needed.
 */
class LiveControlController extends Controller
{
    public function show()
    {
        return view('live-control');
    }

    public function token(Request $request)
    {
        $pin = (string) config('services.live_control.pin');
        $given = $request->input('pin');

        if ($pin === '' || ! \is_string($given) || ! \hash_equals($pin, \trim($given))) {
            return response()->json(['error' => 'Fel PIN-kod'], 403);
        }

        return response()->json(['token' => Firebase::makeLiveControlJwt()]);
    }
}
