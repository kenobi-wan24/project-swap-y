<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'bio',
        'phone',
        'address',
        'city',
        'province',
        'region',
        'zip_code',
        'is_active',
        'is_banned',
        'banned_at',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
        'follower_count',
        'following_count',
        'average_rating',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at'         => 'datetime',
            'is_active'         => 'boolean',
            'is_banned'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    // --- Appends ---

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=E8610A&color=fff';
    }

    public function getFollowerCountAttribute(): int
    {
        return $this->followers()->count();
    }

    public function getFollowingCountAttribute(): int
    {
        return $this->following()->count();
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviewsReceived()->avg('rating') ?? 0, 1);
    }

    // --- Relationships ---

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'following_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'following_id');
    }

    public function sentSwapOffers()
    {
        return $this->hasMany(SwapOffer::class, 'initiator_id');
    }

    public function receivedSwapOffers()
    {
        return $this->hasMany(SwapOffer::class, 'receiver_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
                    ->withPivot('last_read_at')
                    ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)->where('is_active', true)->latestOfMany();
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }
}