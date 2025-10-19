<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class WebhookController extends Controller
{
    public function __invoke(Request $request, $action = null)
    {
        abort_unless(URL::hasValidSignature($request), 403);
        abort_unless(\method_exists($this, $action), 403);

        return $this->$action();
    }

    public function log()
    {
        Log::info('Webhook log');
    }

    public function scheduleRun()
    {
        // Due to php setting disable_functions, we cannot use the schedule:work command.

        // [2025-06-13 11:30:11] production.ERROR: The Process class relies on proc_open, which is not available
        // on your PHP installation. {"exception":"[object] (Symfony\\Component\\Process\\Exception\\LogicException(code: 0):
        // The Process class relies on proc_open, which is not available on your PHP installation. at
        // /customers/9/a/3/goteborgkk.se/httpd.private/web/vendor/symfony/process/Process.php:149)
        // $schedule->command('fortnox:verify-payment --all')->dailyAt('07:30')->timezone('Europe/Stockholm');
        // $schedule->command('fortnox:verify-payment --all')->dailyAt('13:30')->timezone('Europe/Stockholm');

        // The current workaround is to run artisan commands directly in this webhook request cycle. Note that
        // there might be maximum execution time limits imposed by the web server or PHP configuration, probably
        // 30 seconds.

        $currentMinute = now()->timezone('Europe/Stockholm')->format('H:i');

        $hasRunATask = false;

        $output = '';

        if ($currentMinute === '07:20') {
            $hasRunATask = true;
            Artisan::call('gkk:notify-competition-deadlines');
            $output .= Artisan::output();
        }

        if (\in_array($currentMinute, ['07:30', '13:30'])) {
            $hasRunATask = true;
            Artisan::call('fortnox:verify-payment --all');
            $output .= Artisan::output();
        }

        if (! $hasRunATask) {
            return 'No scheduled tasks to run at this time.';
        }

        logger($output);

        return response()->json([
            'message' => 'Scheduled tasks executed successfully.',
            'output' => $output,
        ]);
    }
}
