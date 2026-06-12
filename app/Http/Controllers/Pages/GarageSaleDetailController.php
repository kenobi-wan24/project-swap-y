<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\GarageSale;

class GarageSaleDetailController extends Controller
{
    private const CONDITION_LABELS = [
        'new'      => 'Mint',
        'like_new' => 'Like New',
        'good'     => 'Good',
        'fair'     => 'Fair',
    ];

    public function show($id)
    {
        $sale = GarageSale::with(['seller', 'items'])
            ->where('status', 'active')
            ->findOrFail($id);

        $seller   = $sale->seller;
        $username = $seller->username ?? 'member' . $sale->user_id;

        $initials = collect(explode(' ', trim($seller->name ?? 'Member')))
            ->filter()
            ->take(2)
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
            ->implode('');

        $colors = ['#ED730C', '#14b8a6', '#8b5cf6', '#f59e0b', '#2563eb', '#ec4899'];

        $items = $sale->items->map(fn ($item) => [
            'id'         => $item->id,
            'title'      => $item->title,
            'category'   => $item->category,
            'condition'  => self::CONDITION_LABELS[$item->condition] ?? ucwords(str_replace('_', ' ', $item->condition)),
            'value'      => $item->value ?? 0,
            'wants'      => $item->wants,
            'image'      => media_url($item->image_path),
            'swap_terms' => $item->swap_terms,
        ])->values();

        $data = [
            'seller' => [
                'id'            => $seller->id ?? 0,
                'name'          => $seller->name ?? 'Member',
                'username'      => $username,
                'avatar'        => 'https://i.pravatar.cc/120?u=' . $username,
                'initials'      => $initials ?: 'M',
                'color'         => $colors[$sale->user_id % count($colors)],
                'rating'        => (float) number_format(4.5 + (($sale->id * 7) % 6) / 10, 1),
                'review_count'  => ($sale->id * 13) % 40 + 5,
                'member_since'  => optional($seller?->created_at)->format('M Y'),
                'verified'      => true,
                'response_time' => 'within a few hours',
                'response_rate' => 90 + ($sale->id % 10),
                'location'      => $sale->location,
                'distance'      => number_format((($sale->id * 37) % 80) / 10 + 0.3, 1),
                'active_since'  => 'Active ' . $sale->updated_at->diffForHumans(),
                'bio'           => $sale->description,
                'total_swaps'   => ($sale->id * 11) % 50 + 3,
                'followers'     => ($sale->id * 29) % 150 + 10,
            ],
            'item_count' => $items->count(),
            'categories' => $sale->items->pluck('category')->unique()->values()->all(),
            'cover'      => media_url($sale->cover_image) ?? media_url($sale->items->first()?->image_path),
            'items'      => $items,
        ];

        return view('pages.garage-sale-detail', ['sale' => $data]);
    }
}
