<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Comment;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $comment = Comment::updateOrCreate([
            'user_id' => auth()->id(),
            'recipe_id' => $recipe->id,
        ], [
            'content' => $request->content,
        ]);

        Rating::updateOrCreate([
            'user_id' => auth()->id(),
            'recipe_id' => $recipe->id,
        ], [
            'value' => $request->rating,
        ]);

        return back()->withFragment('comment-' . $comment->id)->with('success', 'Review submitted!');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        Rating::where('user_id', auth()->id())
            ->where('recipe_id', $comment->recipe_id)
            ->delete();

        $comment->delete();

        return back()->withFragment('comments-section')->with('success', 'Comment deleted successfully!');
    }

    public function report(Comment $comment)
    {
        $comment->reports()->create([
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Comment reported successfully!');
    }
}
