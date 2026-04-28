<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'label',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'group_id');
    }
}
