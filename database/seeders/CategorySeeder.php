<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

        $allImages = collect(Storage::disk('public')->files('images/category'));

        foreach ($categories as $category) {
            $slug = Str::slug($category);

            $images = $allImages->filter(function ($path) use ($slug) {
                return Str::startsWith(
                    basename($path),
                    $slug . '-'
                );
            });

            $randomImage = $images->isNotEmpty()
                ? $images->random()
                : null;

            Category::firstOrCreate(
                ['name' => $category],
                [
                    'slug' => $slug,
                    'image' => $randomImage,
                ]
            );
        }
    }
}
