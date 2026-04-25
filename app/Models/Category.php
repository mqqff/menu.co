<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_preferences',
            'category_id',
            'user_id'
        );
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function publishedRecipes()
    {
        return $this->hasMany(Recipe::class)->published();
    }
}
