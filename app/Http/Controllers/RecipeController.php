<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function my(): View
    {
        $recipes = collect([
            (object)[
                'id' => 1,
                'status' => 'draft',
                'rating' => "4.5",
                'saves_count' => "12 users",
                'title' => 'Chocolate Crinkles',
                'image_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c',
                'cook_time' => "21 min",
                'servings' => "16 servings",
            ],
            (object)[
                'id' => 2,
                'status' => 'published',
                'rating' => "4.5",
                'saves_count' => "8 users",
                'title' => 'Oatmeal Peanut Butter',
                'image_url' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e',
                'cook_time' => "15 min",
                'servings' => "4 servings",
            ],
            (object)[
                'id' => 3,
                'status' => 'published',
                'rating' => "4.5",
                'saves_count' => "20 users",
                'title' => 'Peanut Butter Cup Cookies',
                'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff',
                'cook_time' => "10 min",
                'servings' => "40 servings",
            ],
        ]);

        return view('recipes.my', compact('recipes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('recipes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(): View
    {
        $recipe = (object) [
            'title' => 'Chewy Pumpkin Oatmeal Chocolate Chip Cookies',
            'image_url' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e',
            'cook_time' => "12 min",
            'servings' => "24 cookies",
            'saves_count' => "12 users",
            'rating' => 4.5,
            'description' => 'These cookies exist for one reason: pumpkin purée can make “cookie dough” act like batter, which bakes up cakey instead of chewy. I fix that by blotting the pumpkin with paper towels before mixing so the dough behaves like real cookie dough. — This card covers the essentials.',

            'author' => (object) [
                'name' => 'Laurensius Nathanael',
                'username' => 'laurensiusnathanael',
                'avatar_url' => 'https://i.pravatar.cc/100?img=11',
            ],

            'ingredient_groups' => [
                [
                    'label' => 'Dry',
                    'items' => [
                        ['amount' => '1 1/2 cups (188 g)', 'name' => 'all-purpose flour'],
                        ['amount' => '1 tsp', 'name' => 'baking soda'],
                        ['amount' => '1 tsp', 'name' => 'ground cinnamon'],
                        ['amount' => '1 tsp', 'name' => 'pumpkin pie spice'],
                        ['amount' => '1/2 tsp', 'name' => 'salt'],
                        ['amount' => '1 1/2 cups (135 g)', 'name' => 'ld-fashioned whole rolled oats'],
                    ],
                ],
                [
                    'label' => 'Wet',
                    'items' => [
                        ['amount' => '1/2 cup (115 g)', 'name' => 'unsalted butter, melted and slightly cooled'],
                        ['amount' => '1/4 cup (60 ml)', 'name' => 'maple syrup'],
                        ['amount' => '1/2 cup (100 g)', 'name' => 'packed light or dark brown sugar'],
                        ['amount' => '1/4 cup (50 g)', 'name' => 'granulated sugar'],
                        ['amount' => '1', 'name' => 'egg yolk'],
                        ['amount' => '1 cup (240 g)', 'name' => 'pumpkin purée (see Step 3 for blotting)'],
                        ['amount' => '1 tsp', 'name' => 'vanilla extract'],
                        ['amount' => '1 cup (180 g)', 'name' => 'semi-sweet chocolate chips (plus extra for pressing on top, optional)'],
                    ],
                ],
            ],

            'steps' => [
                [
                    'text' => 'Preheat the oven to 350°F (177°C) and line 2 baking sheets with parchment paper. You want parchment here because it helps the cookies spread evenly without sticking.',
                    'image' => null,
                ],
                [
                    'text' => 'Whisk the flour, baking soda, cinnamon, pumpkin pie spice, salt, and oats in a large bowl. The dough should look “oaty” and evenly spiced before any wet ingredients go in.',
                    'image' => null,
                ],
                [
                    'text' => 'Blot the pumpkin: Measure 1 cup pumpkin purée, then press it between paper towels until it looks noticeably drier and less glossy. This is where the texture changes—less free water means a cookie-like chew rather than a cakey lift.',
                    'image' => null,
                ],
                [
                    'text' => 'In a medium bowl, whisk melted butter, maple syrup, brown sugar, and granulated sugar until glossy. Then whisk in the egg yolk, blotted pumpkin, and vanilla until smooth.',
                    'image' => null,
                ],
                [
                    'text' => 'Pour water into the dry mix until the dough looks evenly hydrated and starts to pull away from the bowl. Stop when you no longer see dry flour pockets—overmixing here makes the dough pasty instead of chewy.',
                    'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587',
                ],
                [
                    'text' => 'Fold in chocolate chips. If you want bakery-style tops, save a small handful to press onto the scoops before baking.',
                    'image' => null,
                ],
                [
                    'text' => 'Scoop about 1.5 tablespoons of dough per cookie onto baking sheets, spacing them apart. Lightly flatten each scoop so the spread is predictable (this dough can dome if you don’t).',
                    'image' => null,
                ],
                [
                    'text' => 'Bake 11–13 minutes, until edges look set and lightly browned while centers still look soft. Let cookies cool on the sheet for about 10 minutes before moving—this is when they finish setting without drying out.',
                    'image' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35',
                ],
            ],

            'tips' => 'The texture cue: after blotting, the pumpkin should look thicker , not glossy/wet like yogurt.',

            'comments' => collect([
                (object) [
                    'id' => 1,
                    'rating' => 4,
                    'body' => 'Gave these a shot this afternoon and they’re actually solid. I’m usually pretty hit-or-miss with baking, but the instructions were easy to follow. I ended up adding a tiny bit more salt on top right when they came out of the oven, and it really leveled them up. Thanks for sharing the recipe!',
                    'user' => (object) [
                        'id' => 1,
                        'name' => 'Rahmat Archie',
                        'username' => 'rahmatarchie',
                        'avatar_url' => 'https://i.pravatar.cc/100?img=15',
                    ],
                ],
            ]),
        ];

        $similar_recipes = collect([
            (object)[
                'id' => 1,
                'status' => 'draft',
                'title' => 'Chocolate Crinkles',
                'image_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c',
                'cook_time' => "21 min",
                'servings' => "16 servings",
            ],
            (object)[
                'id' => 2,
                'status' => 'published',
                'title' => 'Oatmeal Peanut Butter',
                'image_url' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e',
                'cook_time' => "15 min",
                'servings' => "4 servings",
            ],
            (object)[
                'id' => 3,
                'status' => 'published',
                'title' => 'Peanut Butter Cup Cookies',
                'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff',
                'cook_time' => "10 min",
                'servings' => "40 servings",
            ],
        ]);

        return view('recipes.show', compact('recipe', 'similar_recipes'));
    }

    public function trendingByCategory(): View
    {
        $categories = collect([
            (object)['name' => 'Cookies',    'image_url' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=600&q=80'],
            (object)['name' => 'Seafood',    'image_url' => 'https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=600&q=80'],
            (object)['name' => 'Soup',       'image_url' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=600&q=80'],
            (object)['name' => 'Toast',      'image_url' => 'https://images.unsplash.com/photo-1619095762086-66b82f914dcf?q=80&w=1025'],
            (object)['name' => 'Asian Food', 'image_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&q=80'],
            (object)['name' => 'Noodles',    'image_url' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=600&q=80'],
        ]);

        return view('recipes.trending-category', compact('categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(): View
    {
        return view('recipes.edit', [
            'isDraft' => false
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
