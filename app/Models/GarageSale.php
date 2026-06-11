<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarageSale extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'cover_image',
        'location', 'latitude', 'longitude', 'status', 'is_promoted',
    ];

    protected $casts = [
        'is_promoted' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(GarageSaleItem::class);
    }
}
