<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class WebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $action = request('action');

        abort_unless(URL::hasValidSignature($request), 403);
        abort_unless(\method_exists($this, $action), 403);

        return $this->$action();
    }

    public function log()
    {
        Log::info('Webhook log');
    }
}
