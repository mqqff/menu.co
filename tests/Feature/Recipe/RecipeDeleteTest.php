<?php

namespace Tests\Feature\Recipe;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\IngredientGroup;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeDeleteTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $otherUser;
    private Category $category;
    private Recipe $recipe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->category = Category::factory()->create();

        Storage::fake('public');

        $this->recipe = Recipe::factory()->create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'status' => 'published',
        ]);

        Storage::disk('public')->put($this->recipe->image, 'fake-image-content');

        IngredientGroup::factory()
            ->count(2)
            ->create(['recipe_id' => $this->recipe->id])
            ->each(function (IngredientGroup $group) {
                Ingredient::factory()->count(2)->create([
                    'group_id' => $group->id,
                ]);
            });

        Step::factory()->count(3)->sequence(
            ['step_order' => 1, 'image' => 'steps/test-step-1.jpg'],
            ['step_order' => 2, 'image' => null],
            ['step_order' => 3, 'image' => 'steps/test-step-3.jpg'],
        )->create(['recipe_id' => $this->recipe->id]);

        Storage::disk('public')->put('steps/test-step-1.jpg', 'fake-step-image-1');
        Storage::disk('public')->put('steps/test-step-3.jpg', 'fake-step-image-3');
    }

    public function test_it_deletes_recipe_successfully(): void
    {
        $this->actingAs($this->owner);

        $response = $this->delete(route('recipes.destroy', $this->recipe));

        $response->assertRedirect(route('recipes.my'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('recipes', 0);
        $this->assertModelMissing($this->recipe);
    }

    public function test_it_cascades_deletes_ingredients_and_steps(): void
    {
        $this->actingAs($this->owner);

        $this->delete(route('recipes.destroy', $this->recipe));

        $this->assertDatabaseCount('ingredient_groups', 0);
        $this->assertDatabaseCount('ingredients', 0);
        $this->assertDatabaseCount('steps', 0);
    }

    public function test_it_deletes_recipe_image_from_storage(): void
    {
        $this->actingAs($this->owner);

        $recipeImage = $this->recipe->image;
        $this->assertTrue(Storage::disk('public')->exists($recipeImage));

        $this->delete(route('recipes.destroy', $this->recipe));

        $this->assertFalse(Storage::disk('public')->exists($recipeImage));
    }

    public function test_it_deletes_step_images_from_storage(): void
    {
        $this->actingAs($this->owner);

        $this->assertTrue(Storage::disk('public')->exists('steps/test-step-1.jpg'));
        $this->assertTrue(Storage::disk('public')->exists('steps/test-step-3.jpg'));

        $this->delete(route('recipes.destroy', $this->recipe));

        $this->assertFalse(Storage::disk('public')->exists('steps/test-step-1.jpg'));
        $this->assertFalse(Storage::disk('public')->exists('steps/test-step-3.jpg'));
    }

    public function test_it_prevents_non_owner_from_deleting_recipe(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->delete(route('recipes.destroy', $this->recipe));

        $response->assertStatus(403);

        $this->assertDatabaseHas('recipes', [
            'id' => $this->recipe->id,
        ]);
    }

    public function test_it_prevents_guest_from_deleting_recipe(): void
    {
        $response = $this->delete(route('recipes.destroy', $this->recipe));

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('recipes', [
            'id' => $this->recipe->id,
        ]);
    }
}
