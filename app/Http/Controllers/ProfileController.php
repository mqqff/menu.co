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

        $recipes = $this->getJson('recipes.json')->recipes ?? [];
        $created_recipes = [];

        foreach ($recipes as $recipe) {
            if ($recipe->author->id == $user->id) {
                $created_recipes[] = $recipe;
            }
        }

        $saved_recipes = [];

        if ($user->id === Auth::id()) {
            $saved_recipes = collect($recipes)->where('author.id', '!=', $user->id)->random(3)->all();
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
}
