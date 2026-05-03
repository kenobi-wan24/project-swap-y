<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'estimated_value',
        'category',
        'condition',
        'location',
        'looking_for',
        'swap_conditions',
        'status',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'swap_conditions'  => 'array',
            'estimated_value'  => 'integer',
            'latitude'  => 'float',
            'longitude' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ItemImage::class)->where('is_primary', true);
    }
}