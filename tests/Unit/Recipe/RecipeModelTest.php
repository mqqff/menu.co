<?php

namespace Tests\Unit\Recipe;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Ingredient;
use App\Models\IngredientGroup;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\Report;
use App\Models\Step;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeModelTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private Recipe $recipe;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();

        $this->recipe = Recipe::factory()->draft()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'image' => 'images/recipe/test-recipe.jpg',
        ]);
    }

    public function test_it_has_image_url_accessor(): void
    {
        $expectedUrl = Storage::url('images/recipe/test-recipe.jpg');
        $this->assertEquals($expectedUrl, $this->recipe->image_url);
        $this->assertStringContainsString('images/recipe/test-recipe.jpg', $this->recipe->image_url);
    }

    public function test_it_scopes_published_recipes(): void
    {
        Recipe::factory()->count(3)->published()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        Recipe::factory()->count(2)->draft()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $publishedRecipes = Recipe::published()->get();
        $this->assertCount(3, $publishedRecipes);

        $publishedRecipes->each(function (Recipe $recipe) {
            $this->assertEquals('published', $recipe->status);
        });
    }

    public function test_it_has_user_relationship(): void
    {
        $this->assertInstanceOf(User::class, $this->recipe->user);
        $this->assertTrue($this->user->is($this->recipe->user));
    }

    public function test_it_has_category_relationship(): void
    {
        $this->assertInstanceOf(Category::class, $this->recipe->category);
        $this->assertTrue($this->category->is($this->recipe->category));
    }

    public function test_it_has_ingredient_groups_relationship(): void
    {
        $groups = IngredientGroup::factory()->count(2)->create([
            'recipe_id' => $this->recipe->id,
        ]);

        $this->assertCount(2, $this->recipe->ingredientGroups);
        $this->assertEquals($groups->pluck('id')->sort()->values()->toArray(),
            $this->recipe->ingredientGroups->pluck('id')->sort()->values()->toArray());
    }

    public function test_it_has_ingredients_relationship(): void
    {
        $group = IngredientGroup::factory()->create([
            'recipe_id' => $this->recipe->id,
        ]);

        $ingredients = Ingredient::factory()->count(3)->create([
            'group_id' => $group->id,
        ]);

        $this->assertCount(3, $this->recipe->ingredients);
    }

    public function test_it_has_steps_relationship(): void
    {
        $steps = Step::factory()->count(3)->sequence(
            ['step_order' => 1],
            ['step_order' => 2],
            ['step_order' => 3],
        )->create(['recipe_id' => $this->recipe->id]);

        $loadedSteps = $this->recipe->steps;
        $this->assertCount(3, $loadedSteps);
        $this->assertEquals([1, 2, 3], $loadedSteps->pluck('step_order')->toArray());
    }

    public function test_it_has_comments_relationship(): void
    {
        $otherUser = User::factory()->create();

        Comment::factory()->create([
            'recipe_id' => $this->recipe->id,
            'user_id' => $this->user->id,
        ]);
        Comment::factory()->create([
            'recipe_id' => $this->recipe->id,
            'user_id' => $otherUser->id,
        ]);

        $this->assertCount(2, $this->recipe->comments);
    }

    public function test_it_has_ratings_relationship(): void
    {
        $otherUser = User::factory()->create();

        Rating::factory()->create([
            'recipe_id' => $this->recipe->id,
            'user_id' => $this->user->id,
        ]);
        Rating::factory()->create([
            'recipe_id' => $this->recipe->id,
            'user_id' => $otherUser->id,
        ]);

        $this->assertCount(2, $this->recipe->ratings);
    }

    public function test_it_has_bookmarks_relationship(): void
    {
        $otherUser = User::factory()->create();

        Bookmark::factory()->create([
            'recipe_id' => $this->recipe->id,
            'user_id' => $this->user->id,
        ]);
        Bookmark::factory()->create([
            'recipe_id' => $this->recipe->id,
            'user_id' => $otherUser->id,
        ]);

        $this->assertCount(2, $this->recipe->bookmarks);
    }

    public function test_it_has_reports_relationship(): void
    {
        $otherUser = User::factory()->create();

        Report::factory()->create([
            'reportable_id' => $this->recipe->id,
            'reportable_type' => Recipe::class,
            'user_id' => $this->user->id,
        ]);
        Report::factory()->create([
            'reportable_id' => $this->recipe->id,
            'reportable_type' => Recipe::class,
            'user_id' => $otherUser->id,
        ]);

        $this->assertCount(2, $this->recipe->reports);
    }

    public function test_it_cascades_deletes_related_models(): void
    {
        $group = IngredientGroup::factory()->create([
            'recipe_id' => $this->recipe->id,
        ]);
        Ingredient::factory()->count(3)->create([
            'group_id' => $group->id,
        ]);
        Step::factory()->count(2)->create([
            'recipe_id' => $this->recipe->id,
        ]);

        $this->recipe->delete();

        $this->assertDatabaseMissing('recipes', ['id' => $this->recipe->id]);
        $this->assertDatabaseMissing('ingredient_groups', ['recipe_id' => $this->recipe->id]);
        $this->assertDatabaseMissing('steps', ['recipe_id' => $this->recipe->id]);
        $this->assertDatabaseCount('ingredients', 0);
    }

    public function test_it_uses_uuid_primary_key(): void
    {
        $this->assertEquals('string', $this->recipe->getKeyType());
        $this->assertFalse($this->recipe->incrementing);
        $this->assertTrue(strlen($this->recipe->id) === 36);
    }

    public function test_it_deletes_recipe_image_on_delete(): void
    {
        Storage::disk('public')->put('images/recipe/test-recipe.jpg', 'fake-content');
        $this->assertTrue(Storage::disk('public')->exists('images/recipe/test-recipe.jpg'));

        $this->recipe->delete();

        $this->assertFalse(Storage::disk('public')->exists('images/recipe/test-recipe.jpg'));
    }

    public function test_it_deletes_step_images_on_delete(): void
    {
        Storage::disk('public')->put('images/recipe/test-recipe.jpg', 'fake-content');

        Step::factory()->create([
            'recipe_id' => $this->recipe->id,
            'step_order' => 1,
            'image' => 'steps/step-1.jpg',
        ]);
        Storage::disk('public')->put('steps/step-1.jpg', 'fake-step-image');

        $this->recipe->delete();

        $this->assertFalse(Storage::disk('public')->exists('steps/step-1.jpg'));
    }
}
