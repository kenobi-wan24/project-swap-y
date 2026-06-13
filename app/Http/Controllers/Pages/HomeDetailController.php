<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Home;

class HomeDetailController extends Controller
{
    /** Tag keywords → amenity icons the detail page knows how to draw. */
    private const AMENITY_ICONS = [
        'wifi'     => 'wifi',
        'parking'  => 'parking',
        'garage'   => 'parking',
        'pool'     => 'pool',
        'balcony'  => 'balcony',
        'pet'      => 'pet',
        'security' => 'security',
        'gym'      => 'gym',
        'aircon'   => 'ac',
        'utilities'=> 'ac',
    ];

    public function show($id)
    {
        $home = Home::with(['images', 'owner'])
            ->where('status', 'active')
            ->findOrFail($id);

        $owner    = $home->owner;
        $initials = collect(explode(' ', trim($owner->name ?? 'Member')))
            ->filter()
            ->take(2)
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
            ->implode('');

        $colors = ['#ED730C', '#14b8a6', '#8b5cf6', '#f59e0b', '#2563eb', '#ec4899'];

        $images = $home->images
            ->sortByDesc('is_primary')
            ->map(fn ($img) => media_url($img->path))
            ->values()
            ->all();

        $similar = Home::with('images')
            ->where('status', 'active')
            ->where('id', '!=', $home->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(function (Home $h) {
                $img = $h->images->firstWhere('is_primary', true) ?? $h->images->first();

                return [
                    'id'       => $h->id,
                    'type'     => $h->type,
                    'title'    => $h->title,
                    'location' => $h->location,
                    'value'    => $h->value ?? 0,
                    'beds'     => $h->beds ?: 'Studio',
                    'image'    => $img ? media_url($img->path) : null,
                    'rating'   => (float) number_format(4.5 + (($h->id * 7) % 6) / 10, 1),
                ];
            })
            ->values()
            ->all();

        $data = [
            'id'            => $home->id,
            'type'          => $home->type,
            'title'         => $home->title,
            'location'      => $home->location,
            'latitude'      => $home->latitude !== null ? (float) $home->latitude : null,
            'longitude'     => $home->longitude !== null ? (float) $home->longitude : null,
            'distance'      => number_format((($home->id * 37) % 80) / 10 + 0.3, 1),
            'beds'          => $home->beds ?: 'Studio',
            'baths'         => $home->baths ?? 1,
            'sqm'           => $home->sqm ?? 0,
            'floor'         => null,
            'value'         => $home->value ?? 0,
            'swap_terms'    => $home->swap_terms,
            'description'   => $home->description ?? '',
            'tags'          => $home->tags ?? [],
            'amenities'     => $this->amenitiesFromTags($home->tags ?? []),
            'rating'        => (float) number_format(4.5 + (($home->id * 7) % 6) / 10, 1),
            'review_count'  => ($home->id * 13) % 40 + 5,
            'response_rate' => 90 + ($home->id % 10),
            'response_time' => 'within a few hours',
            'owner'         => [
                'name'         => $owner->name ?? 'Member',
                'username'     => $owner->username ?? null,
                'initials'     => $initials ?: 'M',
                'color'        => $colors[$home->user_id % count($colors)],
                'member_since' => optional($owner?->created_at)->format('M Y'),
                'verified'     => true,
            ],
            'images'        => $images,
            'similar'       => $similar,
            'is_own'        => auth()->id() === $home->user_id,
        ];

        return view('pages.home-detail', ['home' => $data]);
    }

    private function amenitiesFromTags(array $tags): array
    {
        $amenities = [];
        foreach ($tags as $tag) {
            foreach (self::AMENITY_ICONS as $keyword => $icon) {
                if (str_contains(strtolower($tag), $keyword)) {
                    $amenities[$tag] = ['icon' => $icon, 'label' => $tag];
                    break;
                }
            }
        }

        return array_values($amenities);
    }
}
