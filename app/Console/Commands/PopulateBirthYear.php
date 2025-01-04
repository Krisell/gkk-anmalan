<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PopulateBirthYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkk:populate-birth-year';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populating birth year from licence number ...');

        $users = User::whereNull('birth_year')->whereNotNull('licence_number')->get();

        $users->each(function ($user) {
            $shortYear = (int) \substr($user->licence_number, 0, 2);
            $user->update(['birth_year' => ($shortYear >= 40 ? 1900 : 2000) + $shortYear]);
        });

        $this->info('Birth year successfully populated for '.$users->count().' users.');
    }
}
