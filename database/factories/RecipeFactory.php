<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class RecipeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::inRandomOrder()->value('id'),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'image' => function () {
                $files = Storage::disk('public')->files('images/recipe');

                return count($files)
                    ? $files[array_rand($files)]
                    : null;
            },
            'cook_time' => rand(10, 120) . ' minutes',
            'servings' => rand(1, 6) . ' servings',
            'status' => 'published',
            'tips' => $this->faker->sentence(),
        ];
    }
}
