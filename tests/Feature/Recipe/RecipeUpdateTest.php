<?php

namespace Tests\Feature\Recipe;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\IngredientGroup;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeUpdateTest extends TestCase
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

        IngredientGroup::factory()
            ->count(2)
            ->sequence(
                ['label' => 'Main'],
                ['label' => 'Sauce'],
            )
            ->create(['recipe_id' => $this->recipe->id])
            ->each(function (IngredientGroup $group) {
                Ingredient::factory()->count(2)->create([
                    'group_id' => $group->id,
                ]);
            });

        Step::factory()->count(3)->sequence(
            ['step_order' => 1],
            ['step_order' => 2],
            ['step_order' => 3],
        )->create(['recipe_id' => $this->recipe->id]);
    }

    private function validUpdatePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Updated Pancake Recipe Title',
            'description' => 'Updated description for the pancake recipe.',
            'cook_time' => 45,
            'servings' => 6,
            'status' => 'draft',
            'category_id' => $this->category->id,
            'tips' => 'Updated tips for better cooking.',
            'ingredients' => json_encode([
                ['isSection' => true, 'name' => 'Main'],
                ['name' => 'Whole Wheat Flour', 'amount' => '300 g'],
            ]),
            'steps' => json_encode([
                ['title' => 'Updated step one', 'id' => null],
                ['title' => 'Updated step two', 'id' => null],
            ]),
        ], $overrides);
    }

    public function test_it_updates_recipe_successfully(): void
    {
        $this->actingAs($this->owner);

        $response = $this->patch(
            route('recipes.update', $this->recipe),
            $this->validUpdatePayload()
        );

        $response->assertRedirect(route('recipes.my'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('recipes', [
            'id' => $this->recipe->id,
            'title' => 'Updated Pancake Recipe Title',
            'description' => 'Updated description for the pancake recipe.',
            'cook_time' => 45,
            'servings' => 6,
            'status' => 'draft',
        ]);
    }

    public function test_it_updates_recipe_with_new_image(): void
    {
        $this->actingAs($this->owner);

        $oldImage = $this->recipe->image;
        Storage::disk('public')->put($oldImage, 'fake-content');

        $response = $this->patch(
            route('recipes.update', $this->recipe),
            $this->validUpdatePayload([
                'image' => UploadedFile::fake()->image('new-pancake.jpg'),
            ])
        );

        $response->assertRedirect(route('recipes.my'));

        $this->recipe->refresh();
        $this->assertNotNull($this->recipe->image);
        $this->assertNotEquals($oldImage, $this->recipe->image);
        $this->assertTrue(Storage::disk('public')->exists($this->recipe->image));
        $this->assertFalse(Storage::disk('public')->exists($oldImage));
    }

    public function test_it_updates_recipe_without_changing_image(): void
    {
        $this->actingAs($this->owner);

        $expectedImage = $this->recipe->image;
        Storage::disk('public')->put($expectedImage, 'fake-content');

        $payload = $this->validUpdatePayload();
        unset($payload['image']);

        $this->patch(route('recipes.update', $this->recipe), $payload);

        $this->recipe->refresh();
        $this->assertEquals($expectedImage, $this->recipe->image);
    }

    public function test_it_replaces_ingredients_and_steps_on_update(): void
    {
        $this->actingAs($this->owner);

        $this->patch(
            route('recipes.update', $this->recipe),
            $this->validUpdatePayload()
        );

        $oldGroupIds = IngredientGroup::where('recipe_id', $this->recipe->id)->pluck('id');
        $this->assertCount(1, $oldGroupIds);

        $totalIngredients = Ingredient::whereIn('group_id', $oldGroupIds)->count();
        $this->assertEquals(1, $totalIngredients);

        $this->assertDatabaseCount('steps', 2);
        $steps = Step::where('recipe_id', $this->recipe->id)
            ->orderBy('step_order')
            ->get();
        $this->assertCount(2, $steps);
        $this->assertEquals('Updated step one', $steps[0]->text);
        $this->assertEquals('Updated step two', $steps[1]->text);
    }

    public function test_it_fails_when_title_is_missing_on_update(): void
    {
        $this->actingAs($this->owner);

        $payload = $this->validUpdatePayload();
        unset($payload['title']);
        $response = $this->patch(
            route('recipes.update', $this->recipe),
            $payload
        );

        $response->assertSessionHasErrors(['title']);
    }

    public function test_it_fails_when_steps_is_missing_on_update(): void
    {
        $this->actingAs($this->owner);

        $payload = $this->validUpdatePayload();
        unset($payload['steps']);
        $response = $this->patch(
            route('recipes.update', $this->recipe),
            $payload
        );

        $response->assertSessionHasErrors(['steps']);
    }

    public function test_it_fails_when_description_is_missing_on_update(): void
    {
        $this->actingAs($this->owner);

        $payload = $this->validUpdatePayload();
        unset($payload['description']);
        $response = $this->patch(
            route('recipes.update', $this->recipe),
            $payload
        );

        $response->assertSessionHasErrors(['description']);
    }

    public function test_it_prevents_non_owner_from_updating_recipe(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->patch(
            route('recipes.update', $this->recipe),
            $this->validUpdatePayload()
        );

        $response->assertStatus(403);
    }

    public function test_it_prevents_guest_from_updating_recipe(): void
    {
        $response = $this->patch(
            route('recipes.update', $this->recipe),
            $this->validUpdatePayload()
        );

        $response->assertRedirect(route('login'));
    }
}
