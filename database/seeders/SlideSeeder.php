<?php

namespace Database\Seeders;

use App\Models\Slide;
use Illuminate\Database\Seeder;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slide::factory()->create([
            'data' => [
                'image' => 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png',
            ],
            'order' => 0,
        ]);

        Slide::factory()->create([
            'data' => [
                'text' => 'Glöm inte anmäla er som funktionär till serie 2!',
            ],
            'order' => 1,
        ]);

        Slide::factory()->create([
            'data' => [
                'text' => 'Städschema',
                'image' => 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fsta%CC%88dschema.png?alt=media&token=8d8e4060-08a2-40df-804f-b4cc6b26467a',
            ],
            'order' => 2,
        ]);

        Slide::factory()->create([
            'data' => [
                'image' => 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FLokalregler.png?alt=media&token=f6eacaf7-01cb-4983-81d8-5549679592e4',
            ],
            'order' => 3,
        ]);

        Slide::factory()->create([
            'data' => [
                'text' => 'Veckans meme',
                'image' => 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2F27FE51DA-89FC-43DE-BB99-087315ADBC52.jpg?alt=media&token=e94405b0-5483-4705-b0d6-9c86f37d6a09',
            ],
            'order' => 4,
        ]);

        Slide::factory()->create([
            'data' => [
                'text' => 'Klubblokalen januari 2019',
                'subText' => 'Före golvet gjordes om',
                'image' => 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FlokalenJanuari2019.png?alt=media&token=ab029c13-3d5f-4fff-bd5a-28dcbd4e5181',
            ],
            'order' => 5,
        ]);
    }
}
