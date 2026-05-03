<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── Real stats (expand these as you add more tables) ──────────────
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

        // ── User's active items (for the swap feed section) ───────────────
        $myItems = Item::with('images')
                       ->where('user_id', $user->id)
                       ->where('status', 'active')
                       ->latest()
                       ->take(4)
                       ->get()
                       ->map(function ($item) {
                           $primaryImage = $item->images->firstWhere('is_primary', true)
                                        ?? $item->images->first();
                           return [
                               'id'       => $item->id,
                               'title'    => $item->title,
                               'category' => $item->category,
                               'distance' => '—',
                               'swapper'  => 'You',
                               'initials' => 'ME',
                               'color'    => '#ED730C',
                               'image'    => $primaryImage
                                             ? asset('storage/' . $primaryImage->path)
                                             : null,
                           ];
                       });

        // ── User data (safe subset for frontend) ─────────────────────────
        $userData = [
            'name'     => $user->name,
            'username' => $user->username,
            'email'    => $user->email,
            'avatar'   => null,
        ];

        return view('pages.dashboard', compact('user', 'stats', 'myItems', 'userData'));
    }
}