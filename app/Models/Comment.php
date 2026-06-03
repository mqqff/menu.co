<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Rating;

class Comment extends Model
{
    use HasUuids, HasFactory;

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

    public function checkAndDelete(): void
    {
        if ($this->reports()->count() >= 5) {
            Rating::where('user_id', $this->user_id)
                ->where('recipe_id', $this->recipe_id)
                ->delete();
            $this->delete();
        }
    }
}
