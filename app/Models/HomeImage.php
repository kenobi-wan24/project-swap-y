<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeImage extends Model
{
    protected $fillable = ['home_id', 'path', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function home()
    {
        return $this->belongsTo(Home::class);
    }
}
