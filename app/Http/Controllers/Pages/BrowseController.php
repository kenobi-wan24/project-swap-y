<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['user', 'images'])
            ->where('status', 'active');

        // ── Filters ───────────────────────────────────────────────────────────
        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        match ($request->sort ?? 'recent') {
            'recent' => $query->latest(),
            default  => $query->latest(),
        };

        $items = $query->paginate(12);

        // ── Load current user context for match algorithm ─────────────────────
        $authUser       = Auth::user();
        $userPreference = null;
        $myItems        = collect();

        if ($authUser) {
            $userPreference = $authUser->preference;
            $myItems = Item::where('user_id', $authUser->id)
                ->where('status', 'active')
                ->get(['id', 'title', 'category', 'looking_for']);
        }

        // ── Shape for Vue ─────────────────────────────────────────────────────
        $listings = $items->map(function ($item) use ($authUser, $userPreference, $myItems) {
            $primaryImage = $item->images->firstWhere('is_primary', true)
                         ?? $item->images->first();

            $nameParts = explode(' ', $item->user->name ?? '');
            $initials  = strtoupper(
                substr($nameParts[0] ?? '', 0, 1) .
                substr($nameParts[1] ?? '', 0, 1)
            );

            $colors = ['#ED730C', '#14b8a6', '#8b5cf6', '#f59e0b', '#2563eb', '#ec4899', '#22c55e'];
            $color  = $colors[$item->user_id % count($colors)];

            // Own items → no match score shown
            $matchScore = null;
            if ($authUser && $item->user_id !== $authUser->id) {
                $matchScore = $this->computeMatch($item, $userPreference, $myItems);
            }

            return [
                'id'          => $item->id,
                'title'       => $item->title,
                'image'       => $primaryImage ? asset('storage/' . $primaryImage->path) : null,
                'value'       => $item->estimated_value ?? 0,
                'username'    => $item->user->username ?? '',
                'owner'       => $item->user->name ?? '',
                'avatar'      => $initials,
                'avatarColor' => $color,
                'distance'    => null,
                'category'    => $item->category,
                'condition'   => $item->condition,
                'desc'        => $item->description ?? '',
                'location'    => $item->location ?? '',
                'wants'       => $item->looking_for ?? '',
                'match'       => $matchScore,
                'rating'      => null,
                'badge'       => $item->created_at->isToday() ? 'New' : '',
                'badgeColor'  => $item->created_at->isToday() ? '#149189' : '',
                'promoted'    => false,
                'is_own'      => $authUser ? $item->user_id === $authUser->id : false,
            ];
        });

        // ── Featured: top 5 most recent active items ──────────────────────────
        $featured = Item::with(['user', 'images'])
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) use ($authUser, $userPreference, $myItems) {
                $primaryImage = $item->images->firstWhere('is_primary', true)
                             ?? $item->images->first();
                $nameParts = explode(' ', $item->user->name ?? '');
                $colors    = ['#ED730C', '#14b8a6', '#8b5cf6', '#f59e0b', '#2563eb'];

                $matchScore = null;
                if ($authUser && $item->user_id !== $authUser->id) {
                    $matchScore = $this->computeMatch($item, $userPreference, $myItems);
                }

                return [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'image'       => $primaryImage ? asset('storage/' . $primaryImage->path) : null,
                    'category'    => $item->category,
                    'value'       => $item->estimated_value ?? 0,
                    'match'       => $matchScore,
                    'owner'       => $item->user->name ?? '',
                    'avatarColor' => $colors[$item->user_id % count($colors)],
                    'badge'       => $item->created_at->isToday() ? 'New' : '',
                    'badgeColor'  => $item->created_at->isToday() ? '#149189' : '',
                    'is_own'      => $authUser ? $item->user_id === $authUser->id : false,
                ];
            });

        return view('pages.browse', [
            'listings' => $listings,
            'featured' => $featured,
            'total'    => $items->total(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // [50pts] Mutual swap intent — Item owner wants something that matches your item's title/category, AND you want something that matches their item's title/category
    // [30pts] Mutual = full 50, one-sided = 25pts (partial credit if only one direction matches)
    // [30pts] Category preference — item's category is in your preferred categories
    // [20pts] Value range — item's estimated value fits your min/max
    // ─────────────────────────────────────────────────────────────────────────
    private function computeMatch(Item $item, $pref, $myItems): int
{
    $score = 0;

    // ── Signal 1: Mutual Swap Intent (50pts) ─────────────────────────────────
    // Full 50pts only if BOTH sides want what the other has
    // 25pts if only one direction matches
    $theyWantMe = false; // item owner wants something I have
    $iWantThem  = false; // I want something the item owner has

    if ($item->looking_for && $myItems->isNotEmpty()) {
        $lookingFor = strtolower($item->looking_for);
        $keywords   = preg_split('/[\s,\/]+/', $lookingFor);

        foreach ($myItems as $myItem) {
            $myTitle    = strtolower($myItem->title ?? '');
            $myCategory = strtolower($myItem->category ?? '');

            foreach ($keywords as $keyword) {
                if (strlen($keyword) < 3) continue;
                if (str_contains($myTitle, $keyword) || str_contains($myCategory, $keyword)) {
                    $theyWantMe = true;
                    break 2;
                }
            }
        }
    }

    if ($myItems->isNotEmpty()) {
        $itemTitle    = strtolower($item->title ?? '');
        $itemCategory = strtolower($item->category ?? '');

        foreach ($myItems as $myItem) {
            $myWants = strtolower($myItem->looking_for ?? '');
            if (!$myWants) continue;

            $keywords = preg_split('/[\s,\/]+/', $myWants);
            foreach ($keywords as $keyword) {
                if (strlen($keyword) < 3) continue;
                if (str_contains($itemTitle, $keyword) || str_contains($itemCategory, $keyword)) {
                    $iWantThem = true;
                    break 2;
                }
            }
        }
    }

    if ($theyWantMe && $iWantThem) {
        $score += 50; // Perfect mutual intent
    } elseif ($theyWantMe || $iWantThem) {
        $score += 25; // One-sided interest
    }

    // ── Signal 2: Category preference (30pts) ────────────────────────────────
    if ($pref && !empty($pref->categories)) {
        $preferred = array_map('strtolower', $pref->categories);
        if (in_array(strtolower($item->category), $preferred)) {
            $score += 30;
        }
    } else {
        $score += 15; // No preference set → neutral
    }

    // ── Signal 3: Value range match (20pts) ──────────────────────────────────
    if ($item->estimated_value !== null && $pref) {
        $val    = $item->estimated_value;
        $min    = $pref->value_min ?? 0;
        $max    = $pref->value_max ?? 9999;
        $buffer = ($max - $min) * 0.2;

        if ($val >= $min && $val <= $max) {
            $score += 20;
        } elseif ($val >= ($min - $buffer) && $val <= ($max + $buffer)) {
            $score += 10; // Close enough
        }
    } else {
        $score += 10; // No value data → neutral
    }

    return min(100, max(0, $score));
}
}