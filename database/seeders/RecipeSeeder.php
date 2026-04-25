<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\IngredientGroup;
use App\Models\Ingredient;
use App\Models\Step;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        Recipe::factory(30)->create()->each(function ($recipe) {

            $groups = IngredientGroup::factory(rand(1, 3))
                ->create([
                    'recipe_id' => $recipe->id
                ]);

            foreach ($groups as $group) {
                Ingredient::factory(rand(2, 5))->create([
                    'group_id' => $group->id
                ]);
            }

            $stepsCount = rand(3, 6);

            for ($i = 1; $i <= $stepsCount; $i++) {
                Step::factory()->create([
                    'recipe_id' => $recipe->id,
                    'step_order' => $i
                ]);
            }
        });
    }
}
