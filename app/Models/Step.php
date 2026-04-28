<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Step extends Model
{
    use HasFactory;
    protected $appends = ['image_url'];
    protected $fillable = [
        'recipe_id',
        'step_order',
        'text',
        'image',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
