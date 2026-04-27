<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\IngredientGroup;
use App\Models\Recipe;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function index(): View
    {
        $baseQuery = Recipe::published()
            ->select('recipes.*')
            ->selectSub(
                DB::table('ratings')
                    ->selectRaw('COALESCE(AVG(value), 0)')
                    ->whereColumn('recipe_id', 'recipes.id'),
                'avg_rating'
            )
            ->selectSub(
                DB::table('bookmarks')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('recipe_id', 'recipes.id'),
                'bookmarks_count'
            );

        $trending_recipes = Recipe::fromSub($baseQuery, 'r')
            ->select('*')
            ->selectRaw('(avg_rating * 2) + (bookmarks_count * 0.5) as popularity_score')
            ->orderByDesc('popularity_score')
            ->limit(15)
            ->get()
            ->map(function ($recipe) {
                $recipe->cook_time = $this->formatCookTime($recipe->cook_time);
                return $recipe;
            });

        $recently_added = Recipe::published()
            ->with('category')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($recipe) {
                $recipe->cook_time = $this->formatCookTime($recipe->cook_time);
                return $recipe;
            });

        $trending_categories = Category::withCount(['recipes' => function ($q) {
            $q->where('status', 'published');
        }])
            ->orderByDesc('recipes_count')
            ->take(5)
            ->get();

        return view('recipes.index', compact(
            'trending_recipes',
            'trending_categories',
            'recently_added'
        ));
    }

    public function search(Request $request): View
    {
        $query = $request->input('q');

        $recipes = Recipe::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($q2) use ($query) {
                        $q2->where('name', 'like', "%{$query}%");
                    });
            })
            ->with('category')
            ->select('recipes.*')
            ->selectSub(
                DB::table('ratings')
                    ->selectRaw('COALESCE(AVG(value), 0)')
                    ->whereColumn('recipe_id', 'recipes.id'),
                'avg_rating'
            )
            ->selectSub(
                DB::table('bookmarks')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('recipe_id', 'recipes.id'),
                'bookmarks_count'
            )
            ->orderByDesc(DB::raw('(avg_rating * 2) + (bookmarks_count * 0.5)'))
            ->paginate(100);

        $recipes->getCollection()->transform(function ($recipe) {
            $recipe->cook_time = $this->formatCookTime($recipe->cook_time);
            return $recipe;
        });

        return view('recipes.search', compact('recipes', 'query'));
    }

    public function trendingRecipes(): View
    {
        $recipes = Recipe::published()
            ->with('category')
            ->select('recipes.*')
            ->selectSub(
                DB::table('ratings')
                    ->selectRaw('COALESCE(AVG(value), 0)')
                    ->whereColumn('recipe_id', 'recipes.id'),
                'avg_rating'
            )
            ->selectSub(
                DB::table('bookmarks')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('recipe_id', 'recipes.id'),
                'bookmarks_count'
            )
            ->orderByDesc(DB::raw('(avg_rating * 2) + (bookmarks_count * 0.5)'))
            ->paginate(100);

        $recipes->getCollection()->transform(function ($recipe) {
            $recipe->cook_time = $this->formatCookTime($recipe->cook_time);
            return $recipe;
        });

        return view('recipes.trending', compact('recipes'));
    }

    public function recentlyAdded(): View
    {
        $recipes = Recipe::published()
            ->with('category')
            ->latest()
            ->paginate(100);

        $recipes->getCollection()->transform(function ($recipe) {
            $recipe->cook_time = $this->formatCookTime($recipe->cook_time);
            return $recipe;
        });

        return view('recipes.recent', compact('recipes'));
    }

    public function trendingCategories(): View
    {
        $categories = Category::withCount(['recipes' => function ($q) {
            $q->where('status', 'published');
        }])
            ->orderByDesc('recipes_count')
            ->paginate(15);

        return view('recipes.trending_categories', compact('categories'));
    }

    public function recipeByCategory(Category $category): View
    {
        $recipes = Recipe::published()
            ->where('category_id', $category->id)
            ->latest()
            ->paginate(100);

        $recipes->getCollection()->transform(function ($recipe) {
            $recipe->cook_time = $this->formatCookTime($recipe->cook_time);
            return $recipe;
        });

        return view('recipes.by_category', compact('recipes', 'category'));
    }

    public function my(): View
    {
        $recipes = Recipe::where('user_id', Auth::id())
            ->latest()
            ->get()
            ->loadCount('bookmarks')
            ->loadAvg('ratings', 'value');

        return view('recipes.my', compact('recipes'));
    }

    public function create(): View
    {
        return view('recipes.create', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRecipe($request, true);

        $slug = $this->generateSlug($validated['title']);

        $imagePath = $this->handleImageUpload($request, $slug);

        $recipe = Recipe::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'cook_time' => $validated['cook_time'],
            'servings' => $validated['servings'],
            'status' => $validated['status'],
            'tips' => $validated['tips'],
        ]);

        $this->syncIngredients(
            $recipe->id,
            json_decode($validated['ingredients'] ?? '[]', true)
        );

        $this->syncSteps(
            $recipe->id,
            json_decode($validated['steps'], true),
            $request->file('step_images', []),
            $slug
        );

        return redirect()->route('recipes.my');
    }

    public function edit(Recipe $recipe)
    {
        $recipe->load('ingredientGroups.ingredients', 'steps');

        $cookTime = $recipe->cook_time;
        $recipe->cook_unit = 'minutes';

        if ($cookTime >= 1440) {
            $recipe->cook_time = ceil($cookTime / 1440);
            $recipe->cook_unit = 'days';
        } elseif ($cookTime >= 60) {
            $recipe->cook_time = ceil($cookTime / 60);
            $recipe->cook_unit = 'hours';
        }

        return view('recipes.edit', [
            'recipe' => $recipe,
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $this->validateRecipe($request, false);

        $slug = $this->generateSlug($validated['title']);

        $imagePath = $this->handleImageUpload(
            $request,
            $slug,
            $recipe->image
        );

        $recipe->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'cook_time' => $validated['cook_time'],
            'servings' => $validated['servings'],
            'status' => $validated['status'],
            'tips' => $validated['tips'],
        ]);

        IngredientGroup::where('recipe_id', $recipe->id)->delete();

        foreach ($recipe->steps as $step) {
            if ($step->image) {
                Storage::disk('public')->delete($step->image);
            }
        }
        Step::where('recipe_id', $recipe->id)->delete();

        $this->syncIngredients(
            $recipe->id,
            json_decode($validated['ingredients'] ?? '[]', true)
        );

        $this->syncSteps(
            $recipe->id,
            json_decode($validated['steps'], true),
            $request->file('step_images', []),
            $slug
        );

        return redirect()->route('recipes.my');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load([
            'category',
            'user',
            'ingredientGroups.ingredients',
            'steps',
            'comments.user',
            'comments.rating',
        ])
            ->loadCount('bookmarks')
            ->loadAvg('ratings', 'value');

        $recipe->cook_time = $this->formatCookTime($recipe->cook_time);

        $isBookmarked = $recipe->bookmarks()
            ->where('user_id', auth()->id())
            ->exists();

        $hasReviewed = $recipe->comments()
            ->where('user_id', auth()->id())
            ->exists();

        $similar_recipes = Recipe::with('category')
            ->where('id', '!=', $recipe->id)
            ->where('category_id', $recipe->category_id)
            ->where('status', 'published')
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('recipes.show', compact(
            'recipe',
            'similar_recipes',
            'isBookmarked',
            'hasReviewed'
        ));
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return redirect()->route('recipes.my');
    }

    private function validateRecipe($request, $isCreate = true)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'image' => $isCreate ? 'required|image' : 'nullable|image',
            'cook_time' => 'required',
            'servings' => 'required',
            'status' => 'required|string',
            'ingredients' => 'nullable|string',
            'steps' => 'required|string',
            'description' => 'required|string',
            'tips' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);
    }

    private function generateSlug($title)
    {
        return Str::slug($title) . '-' . Str::random(6);
    }

    private function handleImageUpload($request, $slug, $oldImage = null)
    {
        if ($request->hasFile('image')) {
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            return $request->file('image')
                ->store("images/recipe/{$slug}", 'public');
        }

        return $oldImage;
    }

    private function syncIngredients($recipeId, $ingredientsRaw)
    {
        $currentGroup = null;

        foreach ($ingredientsRaw as $item) {
            if (!empty($item['isSection'])) {
                $currentGroup = IngredientGroup::create([
                    'recipe_id' => $recipeId,
                    'label' => $item['name'] ?: 'Other',
                ]);
            } else {
                if (!$currentGroup) {
                    $currentGroup = IngredientGroup::create([
                        'recipe_id' => $recipeId,
                        'label' => 'Main',
                    ]);
                }

                Ingredient::create([
                    'group_id' => $currentGroup->id,
                    'name' => $item['name'],
                    'amount' => $item['amount'],
                ]);
            }
        }
    }

    private function syncSteps($recipeId, $stepsRaw, $stepImages, $slug)
    {
        foreach ($stepsRaw as $index => $s) {
            $image = null;
            $stepId = $s['id'] ?? null;

            if ($stepId && isset($stepImages[$stepId])) {
                $image = $stepImages[$stepId]->store(
                    "images/recipe/{$slug}/steps",
                    'public'
                );
            }

            Step::create([
                'recipe_id' => $recipeId,
                'step_order' => $index + 1,
                'text' => $s['title'] ?? '',
                'image' => $image,
            ]);
        }
    }
}
