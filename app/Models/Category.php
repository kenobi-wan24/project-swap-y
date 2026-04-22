<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
    ];

    protected $casts = [];

    protected $appends = [
        'is_parent',
    ];

    // --- Appends ---

    public function getIsParentAttribute(): bool
    {
        return is_null($this->parent_id);
    }

    // --- Relationships ---

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}