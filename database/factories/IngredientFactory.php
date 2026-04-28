<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\IngredientGroup;

class IngredientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group_id' => IngredientGroup::factory(),
            'name' => $this->faker->word(),
            'amount' => rand(1, 500) . ' g',
        ];
    }
}
