<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kaos = Category::create(['name' => 'Kaos', 'slug' => 'kaos']);

        Product::create([
            'category_id' => $kaos->id,
            'name' => 'Kaos Polos Hitam',
            'price' => 85000,
            'stock' => 50,
        ]);
    }
}
