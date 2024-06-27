<?php

namespace Database\Factories;

use App\Models\Category;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * Define the model for the factory.
     *
     * @return string
     */
    public function model(): string
    {
        return Category::class;
    }

    public function definition(): array
    {
        $faker = FakerFactory::create();

        $names = [
            'Hidangan Pembuka',
            'Minuman Dingin',
            'Minuman Panas',
            'Hidangan Utama',
            'Hidangan Penutup',
        ];

        return [
            'name' => $names[$this->index], // Use index to get the name in order
            'slug' => Str::slug($names[$this->index]), // Generate slug from ordered name
            'description' => $faker->optional()->paragraph, // Optional description
            'is_active' => $faker->boolean(true), // Always active for these categories
        ];
    }
}
