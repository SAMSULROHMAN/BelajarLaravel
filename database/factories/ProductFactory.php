<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
            'category_id' => Category::factory(), // Otomatis membuat kategori baru untuk setiap produk
            'stock' => $this->faker->numberBetween(0, 50),
            // Tambahkan kolom wajib lainnya di bawah ini, contoh:
            'name' => $this->faker->word(),
            'price' => $this->faker->randomNumber(5),
        ];
    }
}
