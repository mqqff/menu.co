<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        return redirect()->route('auth.login');
    }

    public function login(Request $request)
    {
        return redirect()->route('auth.profile');
    }

    public function profile(): View
    {
        $prefs = ['Seafood', 'Asian Food', 'Noodles'];

        $recipes = $this->getJson('recipes.json')->recipes ?? [];
        $my_recipes = [];

        foreach ($recipes as $recipe) {
            if ($recipe->author->id == $this->userId) {
                $my_recipes[] = $recipe;
            }
        }

        $saved_recipes = collect([
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
            (object)[
                'id' => 4,
                'status' => 'published',
                'title' => 'Peanut Butter Cup Cookies',
                'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff',
                'cook_time' => "10 min",
                'servings' => "40 servings",
            ],
            (object)[
                'id' => 5,
                'status' => 'draft',
                'title' => 'Chocolate Crinkles',
                'image_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c',
                'cook_time' => "21 min",
                'servings' => "16 servings",
            ],
            (object)[
                'id' => 6,
                'status' => 'published',
                'title' => 'Oatmeal Peanut Butter',
                'image_url' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e',
                'cook_time' => "15 min",
                'servings' => "4 servings",
            ],
        ]);

        return view('auth.profile', compact('prefs', 'my_recipes', 'saved_recipes'));
    }
}
