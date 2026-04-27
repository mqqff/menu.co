<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

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
