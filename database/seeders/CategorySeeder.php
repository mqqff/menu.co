<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Seafood',
                'slug' => 'seafood',
                'image' => 'images/category/seafood.jpg',
            ],
            [
                'name' => 'Asian Food',
                'slug' => 'asian-food',
                'image' => 'images/category/asian-food.webp',
            ],
            [
                'name' => 'Noodles',
                'slug' => 'noodles',
                'image' => 'images/category/noodles.jpg',
            ],
            [
                'name' => 'Western',
                'slug' => 'western',
                'image' => 'images/category/western.webp',
            ],
            [
                'name' => 'Dessert',
                'slug' => 'dessert',
                'image' => 'images/category/dessert.webp',
            ],
            [
                'name' => 'Vegetarian',
                'slug' => 'vegetarian',
                'image' => 'images/category/vegetarian.webp',
            ],
            [
                'name' => 'Vegan',
                'slug' => 'vegan',
                'image' => 'images/category/vegan.jpeg',
            ],
            [
                'name' => 'Spicy',
                'slug' => 'spicy',
                'image' => 'images/category/spicy.jpeg',
            ],
            [
                'name' => 'Street Food',
                'slug' => 'street-food',
                'image' => 'images/category/street-food.jpg',
            ],
            [
                'name' => 'Healthy',
                'slug' => 'healthy',
                'image' => 'images/category/healthy.jpg',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => $category['slug'],
                    'image' => $category['image'],
                ]
            );
        }
    }
}
