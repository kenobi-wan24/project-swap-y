<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'description', 'beds', 'baths', 'sqm',
        'value', 'swap_terms', 'tags', 'location', 'latitude', 'longitude', 'status', 'is_promoted',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_promoted'  => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(HomeImage::class);
    }
}
