<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'ABC',
            'url' => 'https://URL.com',
            'document_folder_id' => DocumentFolder::factory(),
        ];
    }
}
