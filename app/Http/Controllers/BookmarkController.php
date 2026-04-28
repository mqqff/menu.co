<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Bookmark;

class BookmarkController extends Controller
{
    public function toggle(Recipe $recipe)
    {
        $userId = auth()->id();

        $existing = Bookmark::where('user_id', $userId)
            ->where('recipe_id', $recipe->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Bookmark::create([
                'user_id' => $userId,
                'recipe_id' => $recipe->id,
            ]);
        }

        return back()->with('success', $existing ? 'Recipe removed from bookmarks.' : 'Recipe added to bookmarks.');
    }
}
