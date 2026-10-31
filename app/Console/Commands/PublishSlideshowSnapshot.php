<?php

namespace App\Console\Commands;

use App\Services\SlideshowSnapshot;
use Illuminate\Console\Command;

class PublishSlideshowSnapshot extends Command
{
    protected $signature = 'gkk:publish-slideshow';

    protected $description = 'Publish the screen slideshow\'s version, so that open screens reload when it changed';

    public function handle(): int
    {
        if (! SlideshowSnapshot::enabled()) {
            $this->warn('FIREBASE_DATABASE_URL is not set, so there is nothing to publish to.');

            return self::SUCCESS;
        }

        if (! SlideshowSnapshot::publish()) {
            $this->error('Publishing failed, see the log.');

            return self::FAILURE;
        }

        $this->info('Published version '.SlideshowSnapshot::build()['version']);

        return self::SUCCESS;
    }
}
