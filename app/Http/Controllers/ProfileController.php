<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        $user->load('preferences');

        $created_recipes = $user->recipes()
            ->latest()
            ->get();

        $saved_recipes = collect();

        if ($user->id === Auth::id()) {
            $saved_recipes = $user->savedRecipes()
                ->latest()
                ->get();
        }

        return view('profile.show', compact('user', 'created_recipes', 'saved_recipes'));
    }

    public function edit(): View
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {

    }

    public function destroy(Request $request, User $user)
    {
        $user->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function createdRecipes(User $user): View
    {
        $recipes = $user->recipes()
            ->latest()
            ->paginate(75);

        $recipes->getCollection()->transform(function ($recipe) {
            $recipe->cook_time = $this->formatCookTime($recipe->cook_time);
            return $recipe;
        });

        return view('profile.created_recipes', compact('user', 'recipes'));
    }
}
