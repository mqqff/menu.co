<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
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
        $baseQuery = Recipe::query()
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

        $trending_recipes = DB::query()
            ->fromSub($baseQuery, 'r')
            ->select('*')
            ->selectRaw('(avg_rating * 2) + (bookmarks_count * 0.5) as popularity_score')
            ->orderByDesc('popularity_score')
            ->limit(15)
            ->get();

        $recently_added = Recipe::with('category')
            ->latest()
            ->take(10)
            ->get();

        $trending_categories = Category::withCount('recipes')
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
        $recipes = $this->getJson('recipes.json')->recipes ?? [];
        $my_recipes = [];

        foreach ($recipes as $recipe) {
            if ($recipe->author->id == Auth::id()) {
                $my_recipes[] = $recipe;
            }
        }

        return view('recipes.my', [
            'recipes' => collect($my_recipes)
        ]);
    }

    public function create(): View
    {
        return view('recipes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'nullable|string',
            'cook_time' => 'required|string',
            'servings' => 'required|string',
            'status' => 'required|string',
            'ingredients' => 'nullable|string',
            'steps' => 'nullable|string',
            'description' => 'nullable|string',
            'tips' => 'nullable|string',
        ]);

        $ingredientsRaw = json_decode($validated['ingredients'] ?? '[]', true) ?? [];
        $stepsRaw = json_decode($validated['steps'] ?? '[]', true) ?? [];

        $ingredient_groups = [];
        $currentGroup = null;

        foreach ($ingredientsRaw as $item) {
            if (!empty($item['isSection'])) {
                if ($currentGroup) {
                    $ingredient_groups[] = $currentGroup;
                }

                $currentGroup = [
                    'label' => $item['value'] ?: 'Other',
                    'items' => []
                ];
            } else {
                if (!$currentGroup) {
                    $currentGroup = [
                        'label' => 'Main',
                        'items' => []
                    ];
                }

                $currentGroup['items'][] = [
                    'amount' => '',
                    'name' => $item['value']
                ];
            }
        }

        if ($currentGroup) {
            $ingredient_groups[] = $currentGroup;
        }

        $steps = [];

        $stepImages = $request->file('step_images', []);

        foreach ($stepsRaw as $s) {
            $image = null;

            $stepId = $s['id'] ?? null;

            if ($stepId && isset($stepImages[$stepId])) {
                $image = $stepImages[$stepId]->store('recipes/steps', 'public');
            }

            $steps[] = [
                'text' => $s['title'] ?? '',
                'image' => $image
            ];
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('recipes', 'public');
        }

        $author = [
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'username' => Auth::user()->username,
            'avatar' => Auth::user()->avatar,
        ];

        $fullRecipe = [
            'id' => Str::uuid()->toString(),
            'status' => $validated['status'],
            'title' => $validated['title'],
            'image_url' => $imagePath ?: null,
            'cook_time' => $validated['cook_time'],
            'servings' => $validated['servings'],
            'author' => $author,
            'rating' => 0,
            'saves_count' => "0 users",
            'description' => $validated['description'],
            'ingredient_groups' => $ingredient_groups,
            'steps' => $steps,
            'tips' => $validated['tips'],
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        $recipePath = 'recipes.json';

        $recipeData = [
            'recipes' => array_merge(
                $this->getJson($recipePath)->recipes ?? [],
                [$fullRecipe]
            )
        ];

        Storage::put($recipePath, json_encode($recipeData, JSON_PRETTY_PRINT));

        return redirect()->route('recipes.my');
    }

    public function edit(string $id)
    {
        $data = $this->getJson('recipes.json', true);

        $recipes = $data['recipes'] ?? [];

        $recipe = collect($recipes)->firstWhere('id', $id);

        if (!$recipe) {
            abort(404);
        }

        if ($recipe['author']['id'] != Auth::id()) {
            abort(403);
        }

        return view('recipes.edit', [
            'recipe' => json_decode(json_encode($recipe))
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'cook_time' => 'required|string',
            'servings' => 'required|string',
            'status' => 'required|string',
            'ingredients' => 'nullable|string',
            'steps' => 'nullable|string',
            'description' => 'nullable|string',
            'tips' => 'nullable|string',
            'image_url' => 'nullable|string',
        ]);

        $ingredientsRaw = json_decode($validated['ingredients'] ?? '[]', true) ?? [];
        $stepsRaw = json_decode($validated['steps'] ?? '[]', true) ?? [];

        $ingredient_groups = [];
        $currentGroup = null;

        foreach ($ingredientsRaw as $item) {
            if (!empty($item['isSection'])) {
                if ($currentGroup) {
                    $ingredient_groups[] = $currentGroup;
                }

                $currentGroup = [
                    'label' => $item['value'] ?: 'Other',
                    'items' => []
                ];
            } else {
                if (!$currentGroup) {
                    $currentGroup = [
                        'label' => 'Main',
                        'items' => []
                    ];
                }

                $currentGroup['items'][] = [
                    'amount' => '',
                    'name' => $item['value']
                ];
            }
        }

        if ($currentGroup) {
            $ingredient_groups[] = $currentGroup;
        }

        $stepImages = $request->file('step_images', []);
        $steps = [];

        $oldSteps = collect($this->getJson('recipes.json')->recipes ?? [])->firstWhere('id', $id)->steps ?? [];

        foreach ($stepsRaw as $index => $s) {
            $stepId = $s['id'] ?? null;

            $image = $oldSteps[$index]->image ?? null;

            if ($stepId && isset($stepImages[$stepId])) {
                $image = $stepImages[$stepId]->store('recipes/steps', 'public');
            }

            $steps[] = [
                'text' => $s['title'] ?? '',
                'image' => $image
            ];
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('recipes', 'public');
            $imagePath = $path;
        }

        $recipePath = 'recipes.json';
        $data = $this->getJson($recipePath);
        $recipes = $data->recipes ?? [];

        foreach ($recipes as $recipe) {
            if ($recipe->id === $id) {
                $recipe->title = $validated['title'];
                $recipe->cook_time = $validated['cook_time'];
                $recipe->servings = $validated['servings'];
                $recipe->status = $validated['status'];
                $recipe->description = $validated['description'];
                $recipe->tips = $validated['tips'];
                $recipe->update_at = now()->toDateTimeString();

                if ($imagePath) {
                    $recipe->image_url = $imagePath;
                }

                $recipe->ingredient_groups = $ingredient_groups;
                $recipe->steps = $steps;

                break;
            }
        }

        Storage::put($recipePath, json_encode(['recipes' => $recipes], JSON_PRETTY_PRINT));

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

        return view('recipes.show', [
            'recipe' => $recipe,
            'similar_recipes' => $similar_recipes,
            'isBookmarked' => $isBookmarked,
        ]);
    }

    public function trendingByCategory(Request $request)
    {

    }

    public function destroy(string $id)
    {
        $data = $this->getJson('recipes.json', true);

        $recipes = $data['recipes'] ?? [];

        $recipeIndex = collect($recipes)->search(fn($r) => $r['id'] == $id);

        if ($recipeIndex === false) {
            abort(404);
        }

        if ($recipes[$recipeIndex]['author']['id'] != Auth::id()) {
            abort(403);
        }

        array_splice($recipes, $recipeIndex, 1);

        Storage::put('recipes.json', json_encode(['recipes' => $recipes], JSON_PRETTY_PRINT));

        return redirect()->route('recipes.my');
    }
}
