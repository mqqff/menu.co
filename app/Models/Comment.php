<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Comment extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'recipe_id',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'user_id', 'user_id')
            ->whereColumn('recipe_id', 'recipe_id');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
