<?php

namespace Database\Seeders;

use App\User;
use App\Document;
use App\DocumentFolder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Martin',
            'last_name' =>  'Krisell',
            'email' => 'martin.krisell@gmail.com',
            'password' => Hash::make('asdasd'),
            'role' => 'admin',
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' =>  'Super',
            'email' => 'martinkrisell@gmail.com',
            'password' => Hash::make('asdasd'),
            'role' => 'superadmin',
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' =>  'Normal',
            'email' => 'martinnormal@gmail.com',
            'password' => Hash::make('asdasd'),
            'role' => null,
        ]);

        $folder1 = DocumentFolder::create([
            'name' => 'Information',
            'order' => 1
        ]);

        $folder2 = DocumentFolder::create([
            'name' => 'Protokoll 2021',
            'order' => 2
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
