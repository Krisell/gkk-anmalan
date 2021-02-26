<?php

namespace Database\Factories;

use App\DocumentFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFolderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DocumentFolder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'ABC',
            'order' => 0,
        ];
    }
}
