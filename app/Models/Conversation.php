<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'swap_offer_id',
    ];

    protected $appends = [
        'unread_count',
    ];

    public function getUnreadCountAttribute(): int
    {
        return $this->messages()->where('is_deleted', false)->count();
    }

    public function swapOffer()
    {
        return $this->belongsTo(SwapOffer::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
                    ->withPivot('last_read_at')
                    ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->latest();
    }
}