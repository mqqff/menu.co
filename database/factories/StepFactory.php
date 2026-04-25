<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Recipe;

class StepFactory extends Factory
{
    public function definition(): array
    {
        return [
            'recipe_id' => Recipe::factory(),
            'step_order' => 1,
            'text' => $this->faker->sentence(),
        ];
    }
}
