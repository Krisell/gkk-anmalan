<?php

namespace Database\Seeders;

use App\Models\Result;
use Illuminate\Database\Seeder;

class ResultSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure some results are recent to test "new record" functionality
        $recent = now()->subMonths(\rand(2, 8))->format('Y-m-d');

        $maleRecords = [
            ['weight_class' => '66', 'event' => 'Knäböj', 'result' => 185, 'name' => 'Erik Lindberg', 'date' => '2023-03-15'],
            ['weight_class' => '66', 'event' => 'Bänkpress', 'result' => 120, 'name' => 'Erik Lindberg', 'date' => '2023-03-15'],
            ['weight_class' => '66', 'event' => 'Marklyft', 'result' => 215, 'name' => 'Erik Lindberg', 'date' => $recent], // Recent
            ['weight_class' => '66', 'event' => 'Total', 'result' => 520, 'name' => 'Erik Lindberg', 'date' => '2023-03-15'],

            ['weight_class' => '74', 'event' => 'Knäböj', 'result' => 210, 'name' => 'Johan Svensson', 'date' => '2022-11-20'],
            ['weight_class' => '74', 'event' => 'Bänkpress', 'result' => 140, 'name' => 'Marcus Holm', 'date' => '2023-06-10'],
            ['weight_class' => '74', 'event' => 'Marklyft', 'result' => 245, 'name' => 'Johan Svensson', 'date' => '2022-11-20'],
            ['weight_class' => '74', 'event' => 'Total', 'result' => 585, 'name' => 'Johan Svensson', 'date' => '2022-11-20'],

            ['weight_class' => '83', 'event' => 'Knäböj', 'result' => 240, 'name' => 'Andreas Karlsson', 'date' => $recent], // Recent
            ['weight_class' => '83', 'event' => 'Bänkpress', 'result' => 155, 'name' => 'Andreas Karlsson', 'date' => '2023-09-05'],
            ['weight_class' => '83', 'event' => 'Marklyft', 'result' => 270, 'name' => 'Andreas Karlsson', 'date' => '2023-09-05'],
            ['weight_class' => '83', 'event' => 'Total', 'result' => 665, 'name' => 'Andreas Karlsson', 'date' => '2023-09-05'],

            ['weight_class' => '93', 'event' => 'Knäböj', 'result' => 265, 'name' => 'Fredrik Nilsson', 'date' => '2022-05-18'],
            ['weight_class' => '93', 'event' => 'Bänkpress', 'result' => 175, 'name' => 'Peter Eriksson', 'date' => '2023-02-25'],
            ['weight_class' => '93', 'event' => 'Marklyft', 'result' => 295, 'name' => 'Fredrik Nilsson', 'date' => '2022-05-18'],
            ['weight_class' => '93', 'event' => 'Total', 'result' => 720, 'name' => 'Fredrik Nilsson', 'date' => '2022-05-18'],

            ['weight_class' => '105', 'event' => 'Knäböj', 'result' => 285, 'name' => 'Magnus Bergström', 'date' => '2024-01-12'],
            ['weight_class' => '105', 'event' => 'Bänkpress', 'result' => 190, 'name' => 'Magnus Bergström', 'date' => '2024-01-12'],
            ['weight_class' => '105', 'event' => 'Marklyft', 'result' => 310, 'name' => 'Magnus Bergström', 'date' => '2024-01-12'],
            ['weight_class' => '105', 'event' => 'Total', 'result' => 785, 'name' => 'Magnus Bergström', 'date' => '2024-01-12'],

            ['weight_class' => '120', 'event' => 'Knäböj', 'result' => 305, 'name' => 'Karl Johansson', 'date' => '2021-09-30'],
            ['weight_class' => '120', 'event' => 'Bänkpress', 'result' => 210, 'name' => 'Karl Johansson', 'date' => '2021-09-30'],
            ['weight_class' => '120', 'event' => 'Marklyft', 'result' => 330, 'name' => 'Karl Johansson', 'date' => '2021-09-30'],
            ['weight_class' => '120', 'event' => 'Total', 'result' => 845, 'name' => 'Karl Johansson', 'date' => '2021-09-30'],
        ];

        $femaleRecords = [
            ['weight_class' => '47', 'event' => 'Knäböj', 'result' => 95, 'name' => 'Emma Andersson', 'date' => '2023-04-22'],
            ['weight_class' => '47', 'event' => 'Bänkpress', 'result' => 52.5, 'name' => 'Emma Andersson', 'date' => '2023-04-22'],
            ['weight_class' => '47', 'event' => 'Marklyft', 'result' => 115, 'name' => 'Emma Andersson', 'date' => '2023-04-22'],
            ['weight_class' => '47', 'event' => 'Total', 'result' => 262.5, 'name' => 'Emma Andersson', 'date' => '2023-04-22'],

            ['weight_class' => '52', 'event' => 'Knäböj', 'result' => 110, 'name' => 'Sara Lindqvist', 'date' => '2022-08-14'],
            ['weight_class' => '52', 'event' => 'Bänkpress', 'result' => 62.5, 'name' => 'Sara Lindqvist', 'date' => '2022-08-14'],
            ['weight_class' => '52', 'event' => 'Marklyft', 'result' => 130, 'name' => 'Sara Lindqvist', 'date' => '2022-08-14'],
            ['weight_class' => '52', 'event' => 'Total', 'result' => 302.5, 'name' => 'Sara Lindqvist', 'date' => '2022-08-14'],

            ['weight_class' => '57', 'event' => 'Knäböj', 'result' => 125, 'name' => 'Lisa Pettersson', 'date' => '2023-10-03'],
            ['weight_class' => '57', 'event' => 'Bänkpress', 'result' => 72.5, 'name' => 'Lisa Pettersson', 'date' => '2023-10-03'],
            ['weight_class' => '57', 'event' => 'Marklyft', 'result' => 145, 'name' => 'Lisa Pettersson', 'date' => '2023-10-03'],
            ['weight_class' => '57', 'event' => 'Total', 'result' => 342.5, 'name' => 'Lisa Pettersson', 'date' => '2023-10-03'],

            ['weight_class' => '63', 'event' => 'Knäböj', 'result' => 140, 'name' => 'Anna Björk', 'date' => '2022-03-28'],
            ['weight_class' => '63', 'event' => 'Bänkpress', 'result' => 82.5, 'name' => 'Klara Ström', 'date' => $recent], // Recent
            ['weight_class' => '63', 'event' => 'Marklyft', 'result' => 160, 'name' => 'Anna Björk', 'date' => '2022-03-28'],
            ['weight_class' => '63', 'event' => 'Total', 'result' => 380, 'name' => 'Anna Björk', 'date' => '2022-03-28'],

            ['weight_class' => '69', 'event' => 'Knäböj', 'result' => 155, 'name' => 'Maria Gustafsson', 'date' => '2024-02-10'],
            ['weight_class' => '69', 'event' => 'Bänkpress', 'result' => 90, 'name' => 'Maria Gustafsson', 'date' => '2024-02-10'],
            ['weight_class' => '69', 'event' => 'Marklyft', 'result' => 175, 'name' => 'Maria Gustafsson', 'date' => $recent], // Recent
            ['weight_class' => '69', 'event' => 'Total', 'result' => 420, 'name' => 'Maria Gustafsson', 'date' => '2024-02-10'],

            ['weight_class' => '76', 'event' => 'Knäböj', 'result' => 165, 'name' => 'Johanna Ekström', 'date' => '2022-11-05'],
            ['weight_class' => '76', 'event' => 'Bänkpress', 'result' => 97.5, 'name' => 'Johanna Ekström', 'date' => '2022-11-05'],
            ['weight_class' => '76', 'event' => 'Marklyft', 'result' => 185, 'name' => 'Johanna Ekström', 'date' => '2022-11-05'],
            ['weight_class' => '76', 'event' => 'Total', 'result' => 447.5, 'name' => 'Johanna Ekström', 'date' => '2022-11-05'],

            ['weight_class' => '84', 'event' => 'Knäböj', 'result' => 175, 'name' => 'Helena Larsson', 'date' => '2023-05-20'],
            ['weight_class' => '84', 'event' => 'Bänkpress', 'result' => 105, 'name' => 'Helena Larsson', 'date' => $recent], // Recent
            ['weight_class' => '84', 'event' => 'Marklyft', 'result' => 195, 'name' => 'Helena Larsson', 'date' => '2023-05-20'],
            ['weight_class' => '84', 'event' => 'Total', 'result' => 475, 'name' => 'Helena Larsson', 'date' => '2023-05-20'],
        ];

        foreach ($maleRecords as $record) {
            Result::create([
                'gender' => 'M',
                'weight_class' => $record['weight_class'],
                'event' => $record['event'],
                'result' => $record['result'],
                'name' => $record['name'],
                'competition_date' => $record['date'],
            ]);
        }

        foreach ($femaleRecords as $record) {
            Result::create([
                'gender' => 'F',
                'weight_class' => $record['weight_class'],
                'event' => $record['event'],
                'result' => $record['result'],
                'name' => $record['name'],
                'competition_date' => $record['date'],
            ]);
        }
    }
}
