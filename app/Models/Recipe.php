<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;

class Recipe extends Model
{
    use HasUuids, HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $appends = ['image_url'];

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'image',
        'cook_time',
        'servings',
        'status',
        'tips',
    ];

    protected static function booted()
    {
        static::deleting(function ($recipe) {
            if ($recipe->image) {
                Storage::disk('public')->delete($recipe->image);
            }

            foreach ($recipe->steps as $step) {
                if ($step->image) {
                    Storage::disk('public')->delete($step->image);
                }
            }

            $recipe->steps()->delete();

            $recipe->ingredientGroups()->each(function ($group) {
                $group->ingredients()->delete();
            });

            $recipe->ingredientGroups()->delete();
        });
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function ingredientGroups()
    {
        return $this->hasMany(IngredientGroup::class);
    }

    public function ingredients()
    {
        return $this->hasManyThrough(
            Ingredient::class,
            IngredientGroup::class,
            'recipe_id',
            'group_id',
            'id',
            'id'
        );
    }

    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('step_order');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
