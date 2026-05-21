<?php

namespace Tests\Feature\Recipe;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $otherUser;
    private Category $category;
    private Recipe $publishedRecipe;
    private Recipe $draftRecipe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->category = Category::factory()->create();

        $this->publishedRecipe = Recipe::factory()->published()->create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
        ]);

        $this->draftRecipe = Recipe::factory()->draft()->create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_it_allows_anyone_to_view_published_recipe(): void
    {
        $response = $this->get(route('recipes.show', $this->publishedRecipe));

        $response->assertStatus(200);
    }

    public function test_it_allows_authenticated_user_to_view_published_recipe(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->get(route('recipes.show', $this->publishedRecipe));

        $response->assertStatus(200);
    }

    public function test_it_allows_owner_to_view_own_draft_recipe(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('recipes.show', $this->draftRecipe));

        $response->assertStatus(200);
    }

    public function test_it_disallows_viewing_draft_recipe_by_non_owner(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->get(route('recipes.show', $this->draftRecipe));

        $response->assertStatus(404);
    }

    public function test_it_disallows_viewing_draft_recipe_by_guest(): void
    {
        $response = $this->get(route('recipes.show', $this->draftRecipe));

        $response->assertStatus(404);
    }

    public function test_it_allows_owner_to_edit_recipe(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('recipes.edit', $this->publishedRecipe));

        $response->assertStatus(200);
    }

    public function test_it_prevents_non_owner_from_editing_recipe(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->get(route('recipes.edit', $this->publishedRecipe));

        $response->assertStatus(403);
    }

    public function test_it_prevents_guest_from_editing_recipe(): void
    {
        $response = $this->get(route('recipes.edit', $this->publishedRecipe));

        $response->assertRedirect(route('login'));
    }

    public function test_it_allows_owner_to_view_my_recipes(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('recipes.my'));

        $response->assertStatus(200);
    }

    public function test_it_prevents_guest_from_viewing_my_recipes(): void
    {
        $response = $this->get(route('recipes.my'));

        $response->assertRedirect(route('login'));
    }

    public function test_it_allows_owner_to_access_create_form(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('recipes.create'));

        $response->assertStatus(200);
    }

    public function test_it_prevents_guest_from_accessing_create_form(): void
    {
        $response = $this->get(route('recipes.create'));

        $response->assertRedirect(route('login'));
    }
}
