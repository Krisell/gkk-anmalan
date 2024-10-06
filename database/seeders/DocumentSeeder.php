<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentFolder;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $folder1 = DocumentFolder::create([
            'name' => 'Information',
            'order' => 1,
        ]);

        $folder2 = DocumentFolder::create([
            'name' => 'Protokoll 2021',
            'order' => 2,
        ]);

        Document::create([
            'name' => 'NNR',
            'url' => 'https://www.livsmedelsverket.se/globalassets/publikationsdatabas/andra-sprak/nordic-nutrition-recommendations-2012.pdf?AspxAutoDetectCookieSupport=1',
            'document_folder_id' => $folder1->id,
        ]);

        Document::create([
            'name' => 'NNR 2',
            'url' => 'https://www.livsmedelsverket.se/globalassets/publikationsdatabas/andra-sprak/nordic-nutrition-recommendations-2012.pdf?AspxAutoDetectCookieSupport=1',
            'document_folder_id' => $folder1->id,
        ]);

        Document::create([
            'name' => 'NNR 2',
            'url' => 'https://www.livsmedelsverket.se/globalassets/publikationsdatabas/andra-sprak/nordic-nutrition-recommendations-2012.pdf?AspxAutoDetectCookieSupport=1',
            'document_folder_id' => $folder2->id,
        ]);
    }
}
