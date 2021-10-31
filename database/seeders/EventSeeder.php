<?php

namespace Database\Seeders;

use App\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::factory(3)->create([
            'date' => now()->addDays(10),
        ]);
    }
}