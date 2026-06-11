<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'user_id', 'title', 'category', 'description', 'rate', 'rate_type',
        'delivery', 'swap_for', 'tags', 'location', 'latitude', 'longitude', 'status', 'is_promoted',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_promoted'  => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class);
    }
}
