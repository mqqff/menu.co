<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRecipeIsPublishedOrOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $recipe = $request->route('recipe');

        if (
            $recipe->status !== 'published' &&
            (!auth()->check() || auth()->id() !== $recipe->user_id)
        ) {
            abort(404);
        }

        return $next($request);
    }
}
