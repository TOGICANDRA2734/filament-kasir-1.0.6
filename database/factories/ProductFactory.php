<?php

namespace Database\Factories;

use App\Models\Product;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProductFactory extends Factory
{
    /**
     * Define the model for the factory.
     *
     * @return string
     */
    public function model(): string
    {
        return Product::class;
    }

    public function definition(): array
    {
        $faker = FakerFactory::create();

        return [
            'category_id' => rand(1, 10), // Assuming you have 10 categories
            'name' => $faker->sentence(2),
            'slug' => Product::generateUniqueSlug($faker->unique()->slug), // Use your existing logic for unique slug generation
            'quantity' => $faker->randomNumber(2), // Generate random quantity between 0 and 99
            'price' => $faker->randomNumber(5), // Generate random price (adjust digits for desired range)
            'is_active' => $faker->boolean(80), // Set 80% chance for active product
            'image' => $faker->imageUrl(640, 480, 'products'), // Generate placeholder image URL
        ];
    }
}
