<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'name',
        'amount',
    ];

    public function group()
    {
        return $this->belongsTo(IngredientGroup::class, 'group_id');
    }
}
