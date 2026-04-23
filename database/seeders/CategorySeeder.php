<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Seafood',
            'Asian Food',
            'Noodles',
            'Western',
            'Dessert',
            'Vegetarian',
            'Vegan',
            'Spicy',
            'Street Food',
            'Healthy',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category],
                ['slug' => Str::slug($category)]
            );
        }
    }
}
