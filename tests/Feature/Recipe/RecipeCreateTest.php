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

class RecipeCreateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();

        Storage::fake('public');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Delicious Pancake Recipe',
            'description' => 'A very delicious pancake recipe description.',
            'image' => UploadedFile::fake()->image('pancake.jpg'),
            'cook_time' => 30,
            'servings' => 4,
            'status' => 'published',
            'category_id' => $this->category->id,
            'tips' => 'Use fresh ingredients for best results.',
            'ingredients' => json_encode([
                ['isSection' => true, 'name' => 'Main'],
                ['name' => 'Flour', 'amount' => '200 g'],
                ['name' => 'Sugar', 'amount' => '100 g'],
            ]),
            'steps' => json_encode([
                ['title' => 'Mix all dry ingredients', 'id' => null],
                ['title' => 'Combine with wet ingredients', 'id' => null],
                ['title' => 'Cook on medium heat for 3 minutes', 'id' => null],
            ]),
        ], $overrides);
    }

    public function test_it_creates_recipe_successfully(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('recipes.store'), $this->validPayload());

        $response->assertRedirect(route('recipes.my'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('recipes', [
            'title' => 'Delicious Pancake Recipe',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'cook_time' => 30,
            'servings' => 4,
            'status' => 'published',
        ]);

        $recipe = Recipe::where('title', 'Delicious Pancake Recipe')->first();
        $this->assertNotNull($recipe);
        $this->assertNotNull($recipe->image);
        $this->assertTrue(Storage::disk('public')->exists($recipe->image));
    }

    public function test_it_creates_recipe_with_ingredients_and_steps(): void
    {
        $this->actingAs($this->user);

        $this->post(route('recipes.store'), $this->validPayload());

        $recipe = Recipe::where('title', 'Delicious Pancake Recipe')->first();

        $this->assertDatabaseCount('ingredient_groups', 1);
        $ingredientGroupIds = IngredientGroup::where('recipe_id', $recipe->id)->pluck('id');
        $this->assertCount(1, $ingredientGroupIds);
        $this->assertDatabaseCount('ingredients', 2);
        $this->assertEquals(2, Ingredient::whereIn('group_id', $ingredientGroupIds)->count());

        $this->assertDatabaseCount('steps', 3);
        $steps = Step::where('recipe_id', $recipe->id)->orderBy('step_order')->get();
        $this->assertCount(3, $steps);
        $this->assertEquals(1, $steps[0]->step_order);
        $this->assertEquals(2, $steps[1]->step_order);
        $this->assertEquals(3, $steps[2]->step_order);
    }

    public function test_it_handles_image_upload_correctly(): void
    {
        $this->actingAs($this->user);

        $this->post(route('recipes.store'), $this->validPayload());

        $recipe = Recipe::where('title', 'Delicious Pancake Recipe')->first();

        $this->assertStringStartsWith('images/recipe/', $recipe->image);
        $this->assertTrue(Storage::disk('public')->exists($recipe->image));
    }

    public function test_it_creates_draft_recipe_successfully(): void
    {
        $this->actingAs($this->user);

        $this->post(route('recipes.store'), $this->validPayload([
            'status' => 'draft',
        ]));

        $this->assertDatabaseHas('recipes', [
            'title' => 'Delicious Pancake Recipe',
            'status' => 'draft',
        ]);
    }

    public function test_it_creates_recipe_without_ingredients(): void
    {
        $this->actingAs($this->user);

        $this->post(route('recipes.store'), $this->validPayload([
            'ingredients' => null,
        ]));

        $this->assertDatabaseCount('ingredient_groups', 0);
        $this->assertDatabaseCount('ingredients', 0);
    }

    public function test_it_fails_when_title_is_missing(): void
    {
        $this->actingAs($this->user);

        $payload = $this->validPayload();
        unset($payload['title']);
        $response = $this->post(route('recipes.store'), $payload);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_it_fails_when_image_is_missing(): void
    {
        $this->actingAs($this->user);

        $payload = $this->validPayload();
        unset($payload['image']);
        $response = $this->post(route('recipes.store'), $payload);

        $response->assertSessionHasErrors(['image']);
    }

    public function test_it_fails_when_image_is_not_valid(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('recipes.store'), $this->validPayload([
            'image' => UploadedFile::fake()->create('document.pdf', 100),
        ]));

        $response->assertSessionHasErrors(['image']);
    }

    public function test_it_fails_when_steps_is_missing(): void
    {
        $this->actingAs($this->user);

        $payload = $this->validPayload();
        unset($payload['steps']);
        $response = $this->post(route('recipes.store'), $payload);

        $response->assertSessionHasErrors(['steps']);
    }

    public function test_it_fails_when_description_is_missing(): void
    {
        $this->actingAs($this->user);

        $payload = $this->validPayload();
        unset($payload['description']);
        $response = $this->post(route('recipes.store'), $payload);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_it_fails_when_cook_time_is_missing(): void
    {
        $this->actingAs($this->user);

        $payload = $this->validPayload();
        unset($payload['cook_time']);
        $response = $this->post(route('recipes.store'), $payload);

        $response->assertSessionHasErrors(['cook_time']);
    }

    public function test_it_fails_when_servings_is_missing(): void
    {
        $this->actingAs($this->user);

        $payload = $this->validPayload();
        unset($payload['servings']);
        $response = $this->post(route('recipes.store'), $payload);

        $response->assertSessionHasErrors(['servings']);
    }

    public function test_it_fails_when_status_is_missing(): void
    {
        $this->actingAs($this->user);

        $payload = $this->validPayload();
        unset($payload['status']);
        $response = $this->post(route('recipes.store'), $payload);

        $response->assertSessionHasErrors(['status']);
    }

    public function test_it_fails_when_category_id_is_invalid(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('recipes.store'), $this->validPayload([
            'category_id' => 9999,
        ]));

        $response->assertSessionHasErrors(['category_id']);
    }

    public function test_it_prevents_guest_from_creating_recipe(): void
    {
        $response = $this->post(route('recipes.store'), $this->validPayload());

        $response->assertRedirect(route('login'));
    }

    public function test_it_accepts_tips_as_nullable(): void
    {
        $this->actingAs($this->user);

        $this->post(route('recipes.store'), $this->validPayload([
            'tips' => null,
        ]));

        $recipe = Recipe::where('title', 'Delicious Pancake Recipe')->first();
        $this->assertNull($recipe->tips);
    }
}
