<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Home;
use App\Models\GarageSale;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── Real stats ────────────────────────────────────────────────────────
        $activeListings = Item::where('user_id', $user->id)
                              ->where('status', 'active')
                              ->count();

        $stats = [
            [
                'key'    => 'listings',
                'label'  => 'Active Listings',
                'value'  => (string) $activeListings,
                'accent' => '#ED730C',
                'bg'     => '#fff7ed',
                'border' => '#fed7aa',
            ],
            // These will be real once you have swaps / messages tables
            [
                'key'    => 'swaps',
                'label'  => 'Completed Swaps',
                'value'  => '0',
                'accent' => '#14b8a6',
                'bg'     => '#f0fdf4',
                'border' => '#a7f3d0',
            ],
            [
                'key'    => 'negotiations',
                'label'  => 'Ongoing Negotiations',
                'value'  => '0',
                'accent' => '#8b5cf6',
                'bg'     => '#f5f3ff',
                'border' => '#ddd6fe',
            ],
            [
                'key'    => 'views',
                'label'  => 'Item Views Today',
                'value'  => '0',
                'accent' => '#f59e0b',
                'bg'     => '#fffbeb',
                'border' => '#fde68a',
            ],
        ];

        // ── My Listings — real items with images ──────────────────────────────
        $myListings = Item::with('images')
            ->where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($item) {
                $primaryImage = $item->images->firstWhere('is_primary', true)
                             ?? $item->images->first();
                return [
                    'id'        => $item->id,
                    'title'     => $item->title,
                    'category'  => $item->category,
                    'condition' => $item->condition,
                    'desc'      => $item->description ?? '',
                    'value'     => $item->estimated_value ?? 0,
                    'wants'     => $item->looking_for ?? '',
                    'status'    => $item->status ?? 'active',
                    'views'     => $item->views ?? 0,
                    'image'     => $primaryImage
                                   ? media_url($primaryImage->path)
                                   : null,
                ];
            });

        // ── Swap Requests — empty until SwapRequest model exists ──────────────
        // TODO: Replace with real query once swap_requests table is created:
        // $swapRequests = SwapRequest::with(['requester', 'offeredItem', 'wantedItem'])
        //     ->where('target_user_id', $user->id)
        //     ->where('status', 'pending')
        //     ->latest()
        //     ->take(6)
        //     ->get()
        //     ->map(fn($r) => [ ... ]);
        $swapRequests = [];

        // ── Smart Matches — empty until matching logic exists ──────────────────
        // TODO: Replace with real match query
        $smartMatches = [];

        // ── Messages — empty until messages table exists ──────────────────────
        // TODO: Replace with real query
        $messages = [];

        // ── User data (safe subset for frontend) ─────────────────────────────
        $userData = [
            'name'     => $user->name,
            'username' => $user->username,
            'email'    => $user->email,
            'avatar'   => null,
        ];

        // ── All listings, grouped by type (for the "My Listings" tab) ─────────
        $listings = [
            'items'       => $this->mapItems(Item::with('images')->where('user_id', $user->id)->latest()->get()),
            'homes'       => $this->mapHomes(Home::with('images')->where('user_id', $user->id)->latest()->get()),
            'garageSales' => $this->mapGarageSales(GarageSale::withCount('items')->where('user_id', $user->id)->latest()->get()),
            'services'    => $this->mapServices(Service::with('images')->where('user_id', $user->id)->latest()->get()),
        ];

        return view('pages.dashboard', compact(
            'user', 'stats', 'myListings', 'listings',
            'swapRequests', 'smartMatches', 'messages', 'userData'
        ));
    }

    private function mapItems($rows)
    {
        return $rows->map(function ($it) {
            $img = $it->images->firstWhere('is_primary', true) ?? $it->images->first();
            return [
                'id'          => $it->id,
                'title'       => $it->title,
                'image'       => $img ? media_url($img->path) : null,
                'category'    => $it->category,
                'meta'        => ucwords(str_replace('_', ' ', $it->condition ?? '')),
                'price_label' => $it->estimated_value ? '₱' . number_format($it->estimated_value) : null,
                'status'      => $it->status ?? 'active',
                'is_promoted' => (bool) ($it->is_promoted ?? false),
                'url'         => '/item/' . $it->id,
                'created_at'  => $it->created_at->diffForHumans(),
                'description' => $it->description ?? '',
                'price'       => $it->estimated_value,
            ];
        })->values();
    }

    private function mapHomes($rows)
    {
        return $rows->map(function ($h) {
            $img   = $h->images->firstWhere('is_primary', true) ?? $h->images->first();
            $beds  = $h->beds ? ($h->beds === 'Studio' ? 'Studio' : $h->beds . ' bed') : '';
            $meta  = trim($beds . ($h->baths ? ' · ' . $h->baths . ' bath' : ''), ' ·');
            $price = $h->value ? ($h->type === 'Sell' ? '₱' . number_format($h->value) : '₱' . number_format($h->value) . '/mo') : null;
            return [
                'id'          => $h->id,
                'title'       => $h->title,
                'image'       => $img ? media_url($img->path) : null,
                'category'    => $h->type,
                'meta'        => $meta,
                'price_label' => $price,
                'status'      => $h->status ?? 'active',
                'is_promoted' => (bool) $h->is_promoted,
                'url'         => '/homes/' . $h->id,
                'created_at'  => $h->created_at->diffForHumans(),
                'description' => $h->description ?? '',
                'price'       => $h->value,
            ];
        })->values();
    }

    private function mapGarageSales($rows)
    {
        return $rows->map(function ($g) {
            return [
                'id'          => $g->id,
                'title'       => $g->title,
                'image'       => media_url($g->cover_image),
                'category'    => 'Garage Sale',
                'meta'        => $g->items_count . ' item' . ($g->items_count === 1 ? '' : 's'),
                'price_label' => null,
                'status'      => $g->status ?? 'active',
                'is_promoted' => (bool) $g->is_promoted,
                'url'         => '/garage-sale/' . $g->id,
                'created_at'  => $g->created_at->diffForHumans(),
                'description' => $g->description ?? '',
                'price'       => null,
            ];
        })->values();
    }

    private function mapServices($rows)
    {
        return $rows->map(function ($s) {
            $img   = $s->images->firstWhere('is_primary', true) ?? $s->images->first();
            $price = $s->rate ? '₱' . number_format($s->rate) . ($s->rate_type ? ' · ' . $s->rate_type : '') : null;
            return [
                'id'          => $s->id,
                'title'       => $s->title,
                'image'       => $img ? media_url($img->path) : null,
                'category'    => $s->category,
                'meta'        => $s->delivery,
                'price_label' => $price,
                'status'      => $s->status ?? 'active',
                'is_promoted' => (bool) $s->is_promoted,
                'url'         => '/services/' . $s->id,
                'created_at'  => $s->created_at->diffForHumans(),
                'description' => $s->description ?? '',
                'price'       => $s->rate,
            ];
        })->values();
    }
}