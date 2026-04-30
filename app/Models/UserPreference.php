<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'categories',
        'value_min',
        'value_max',
        'max_distance',
        'notification_frequency',
        'intent',
    ];

    protected function casts(): array
    {
        return [
            'categories' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}