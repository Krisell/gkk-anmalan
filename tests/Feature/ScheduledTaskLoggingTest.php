<?php

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

function scheduledTask(string $command)
{
    return app(Schedule::class)->command($command);
}

test('a finished scheduled task is logged with its command and runtime', function () {
    Log::shouldReceive('info')->once()->with('Scheduled task gkk:publish-slideshow finished in 1.2 s');

    event(new ScheduledTaskFinished(scheduledTask('gkk:publish-slideshow'), 1.234));
});

test('a scheduled task that exits with an error is logged as a warning', function () {
    $task = scheduledTask('fortnox:verify-payment --all');
    $task->exitCode = 1;

    Log::shouldReceive('warning')->once()->with('Scheduled task fortnox:verify-payment --all finished in 0.5 s with exit code 1');

    event(new ScheduledTaskFinished($task, 0.5));
});

test('a scheduled task that throws is logged as an error', function () {
    Log::shouldReceive('error')->once()->with('Scheduled task gkk:notify-competition-deadlines failed: Boom');

    event(new ScheduledTaskFailed(scheduledTask('gkk:notify-competition-deadlines'), new RuntimeException('Boom')));
});
