<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Recipe;

class IngredientGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'recipe_id' => Recipe::factory(),
            'label' => $this->faker->randomElement([
                'Main',
                'Sauce',
                'Topping'
            ]),
        ];
    }
}
