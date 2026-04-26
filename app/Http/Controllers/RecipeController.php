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
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

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
            ->published()
            ->select('*')
            ->selectRaw('(avg_rating * 2) + (bookmarks_count * 0.5) as popularity_score')
            ->orderByDesc('popularity_score')
            ->limit(15)
            ->get();

        $recently_added = Recipe::published()
            ->with('category')
            ->latest()
            ->take(10)
            ->get();

        $trending_categories = Category::withCount(['recipes' => function ($q) {
            $q->where('status', 'published');
        }])
            ->orderByDesc('recipes_count')
            ->take(5)
            ->get();

        return view('recipes.index', [
            'trending_recipes' => $trending_recipes,
            'trending_categories' => $trending_categories,
            'recently_added' => $recently_added,
        ]);
    }

    public function my(): View
    {
        $recipes = Recipe::where('user_id', Auth::id())->latest()->get();

        return view('recipes.my', [
            'recipes' => collect($recipes)
        ]);
    }

    public function create(): View
    {
        $categories = Category::all();

        return view('recipes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image',
            'cook_time' => 'required|integer',
            'servings' => 'required|integer',
            'status' => 'required|string',
            'ingredients' => 'nullable|string',
            'steps' => 'required|string',
            'description' => 'required|string',
            'tips' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $imagePath = null;

        $slug = Str::slug($validated['title']) . '-' . Str::random(6);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/recipe/' . $slug, 'public');
        }

        $recipe = Recipe::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'cook_time' => $validated['cook_time'],
            'servings' => $validated['servings'],
            'status' => $validated['status'],
            'tips' => $validated['tips'],
        ]);

        $ingredientsRaw = json_decode($validated['ingredients'] ?? '[]', true) ?? [];
        $currentGroup = null;

        foreach ($ingredientsRaw as $item) {
            if (!empty($item['isSection'])) {
                $currentGroup = IngredientGroup::create([
                    'recipe_id' => $recipe->id,
                    'label' => $item['name'] ?: 'Other',
                ]);
            } else {
                if (!$currentGroup) {
                    $currentGroup = IngredientGroup::create([
                        'recipe_id' => $recipe->id,
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

        $stepsRaw = json_decode($validated['steps'] ?? '[]', true) ?? [];
        $stepImages = $request->file('step_images', []);

        foreach ($stepsRaw as $index => $s) {
            $image = null;
            $stepId = $s['id'] ?? null;

            if ($stepId && isset($stepImages[$stepId])) {
                $image = $stepImages[$stepId]->store('images/recipe/' . $slug . '/steps', 'public');
            }

            Step::create([
                'recipe_id' => $recipe->id,
                'step_order' => $index + 1,
                'text' => $s['title'] ?? '',
                'image' => $image,
            ]);
        }

        return redirect()->route('recipes.my');
    }

    public function edit(Recipe $recipe)
    {
        $recipe = $recipe->load('ingredientGroups.ingredients', 'steps');
        $categories = Category::all();

        $cookTime = $recipe->cook_time;
        $recipe->cook_unit = 'minutes';

        if ($cookTime >= 1440) {
            $recipe->cook_time = ceil($cookTime / 1440);
            $recipe->cook_unit = 'days';
        } else if ($cookTime >= 60) {
            $recipe->cook_time = ceil($cookTime / 60);
            $recipe->cook_unit = 'hours';
        }

        return view('recipes.edit', compact('recipe', 'categories'));
    }

    public function update(Request $request, Recipe $recipe)
    {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'nullable|image',
                'cook_time' => 'required|string',
                'servings' => 'required|string',
                'status' => 'required|string',
                'ingredients' => 'nullable|string',
                'steps' => 'required|string',
                'description' => 'required|string',
                'tips' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
            ]);

            $imagePath = $recipe->image;

            if ($request->hasFile('image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }

                $slug = Str::slug($validated['title']) . '-' . Str::random(6);
                $imagePath = $request->file('image')->store('images/recipe/' . $slug, 'public');
            }

            $recipe->update([
                'category_id' => $request->category_id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'image' => $imagePath,
                'cook_time' => $validated['cook_time'],
                'servings' => $validated['servings'],
                'status' => $validated['status'],
                'tips' => $validated['tips'],
            ]);

            IngredientGroup::whereIn('id', function ($q) use ($recipe) {
                $q->select('id')->from('ingredient_groups')->where('recipe_id', $recipe->id);
            })->delete();

            $steps = Step::where('recipe_id', $recipe->id)->get();
            foreach ($steps as $step) {
                if ($step->image) {
                    Storage::disk('public')->delete($step->image);
                }
            }
            Step::where('recipe_id', $recipe->id)->delete();

            $ingredientsRaw = json_decode($validated['ingredients'] ?? '[]', true) ?? [];
            $currentGroup = null;

            foreach ($ingredientsRaw as $item) {
                if (!empty($item['isSection'])) {
                    $currentGroup = IngredientGroup::create([
                        'recipe_id' => $recipe->id,
                        'label' => $item['name'] ?: 'Other',
                    ]);
                } else {
                    if (!$currentGroup) {
                        $currentGroup = IngredientGroup::create([
                            'recipe_id' => $recipe->id,
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

            $stepsRaw = json_decode($validated['steps'] ?? '[]', true) ?? [];
            $stepImages = $request->file('step_images', []);

            foreach ($stepsRaw as $index => $s) {
                $image = null;
                $stepId = $s['id'] ?? null;

                if ($stepId && isset($stepImages[$stepId])) {
                    $image = $stepImages[$stepId]->store('images/recipe/' . Str::slug($validated['title']) . '-' . Str::random(6) . '/steps', 'public');
                }

                Step::create([
                    'recipe_id' => $recipe->id,
                    'step_order' => $index + 1,
                    'text' => $s['title'] ?? '',
                    'image' => $image,
                ]);
            }

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

        $recipe->loadCount('bookmarks');

        $isBookmarked = $recipe->bookmarks()
            ->where('user_id', auth()->id())
            ->exists();

        $similar_recipes = Recipe::with('category')
            ->where('id', '!=', $recipe->id)
            ->where('category_id', $recipe->category_id)
            ->where('status', 'published')
            ->inRandomOrder()
            ->take(4)
            ->get();

        $hasReviewed = $recipe->comments()
            ->where('user_id', auth()->id())
            ->exists();

        $cookTime = $recipe->cook_time;

        if ($cookTime >= 1440) {
            $recipe->cook_time = ceil($cookTime / 1440) . ' days';
        } else if ($cookTime >= 60) {
            $recipe->cook_time = ceil($cookTime / 60) . ' hours';
        } else {
            $recipe->cook_time = $cookTime . ' minutes';
        }

        return view('recipes.show', [
            'recipe' => $recipe,
            'similar_recipes' => $similar_recipes,
            'isBookmarked' => $isBookmarked,
            'hasReviewed' => $hasReviewed
        ]);
    }

    public function trendingByCategory(Request $request)
    {

    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('recipes.my');
    }
}
