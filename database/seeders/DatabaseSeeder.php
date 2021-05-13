<?php

namespace Database\Seeders;

use App\Document;
use App\DocumentFolder;
use App\User;
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
            'first_name' => 'Vanlig',
            'last_name' =>  'Dödlig',
            'email' => 'user@example.com',
            'password' => Hash::make('asdasdasd'),
            'role' => null,
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'GKK',
            'last_name' =>  'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('asdasdasd'),
            'role' => 'admin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'GKK',
            'last_name' =>  'Superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('asdasdasd'),
            'role' => 'superadmin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' =>  'Krisell',
            'email' => 'martin.krisell@gmail.com',
            'password' => Hash::make('asdasd'),
            'role' => 'admin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' =>  'Super',
            'email' => 'martinkrisell@gmail.com',
            'password' => Hash::make('asdasd'),
            'role' => 'superadmin',
            'granted_by' => 1,
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' =>  'Normal',
            'email' => 'martinnormal@gmail.com',
            'password' => Hash::make('asdasd'),
            'role' => null,
            'granted_by' => 1,
        ]);

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
