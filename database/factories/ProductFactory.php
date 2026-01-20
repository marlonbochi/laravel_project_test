<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(10),
            'price' => fake()->randomFloat(2, 9.99, 999.99),
            'category' => fake()->randomElement(['Electronics', 'Clothing', 'Books', 'Home', 'Sports', 'Toys']),
            'stock' => fake()->numberBetween(0, 100),
            'sku' => fake()->unique()->bothify('SKU-????-####'),
            'is_active' => fake()->boolean(80), // 80% chance of being true
        ];
    }
}
