<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRecipeOwner
{
    public function handle(Request $request, Closure $next)
    {
        $recipe = $request->route('recipe');

        if (!$recipe) {
            abort(404);
        }

        if ($recipe->user_id !== auth()->id()) {
            abort(403);
        }

        return $next($request);
    }
}
