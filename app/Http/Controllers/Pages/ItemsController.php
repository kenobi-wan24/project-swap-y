<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Support\MatchScorer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public const SECTIONS = ['featured', 'popular', 'nearby', 'near-you'];

    public function __construct(private readonly MatchScorer $matchScorer)
    {
    }

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

        // One real pool feeds the main grid and the horizontal sections
        // (the Vue page slices it by city / distance client-side).
        $items = $query->take(60)->get();

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
        $listings = $items->map(
            fn ($item) => $this->shapeListing($item, $authUser, $userPreference, $myItems)
        );

        // ── Featured: only promoted items ─────────────────────────────────────
        $featured = Item::with(['user', 'images'])
            ->where('status', 'active')
            ->where('is_promoted', true)
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($item) use ($authUser, $userPreference, $myItems) {
                $primaryImage = $item->images->firstWhere('is_primary', true)
                             ?? $item->images->first();
                $colors = ['#ED730C', '#14b8a6', '#8b5cf6', '#f59e0b', '#2563eb'];

                $matchScore = null;
                if ($authUser && $item->user_id !== $authUser->id) {
                    $matchScore = $this->matchScorer->score($item, $userPreference, $myItems);
                }

                return [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'image'       => $primaryImage ? media_url($primaryImage->path) : null,
                    'category'    => $item->category,
                    'value'       => $item->estimated_value ?? 0,
                    'match'       => $matchScore,
                    'owner'       => $item->user->name ?? '',
                    'avatarColor' => $colors[$item->user_id % count($colors)],
                    'badge'       => 'Featured',
                    'badgeColor'  => '#ED730C',
                    'is_own'      => $authUser ? $item->user_id === $authUser->id : false,
                ];
            });

        return view('pages.items', [
            'listings' => $listings,
            'featured' => $featured,
            'total'    => Item::where('status', 'active')->count(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // "See all" view for a horizontal section (featured / popular / nearby / near-you)
    // ─────────────────────────────────────────────────────────────────────────
    public function section(Request $request, string $key)
    {
        abort_unless(in_array($key, self::SECTIONS, true), 404);

        $authUser       = Auth::user();
        $userPreference = null;
        $myItems        = collect();

        if ($authUser) {
            $userPreference = $authUser->preference;
            $myItems = Item::where('user_id', $authUser->id)
                ->where('status', 'active')
                ->get(['id', 'title', 'category', 'looking_for']);
        }

        $query = Item::with(['user', 'images'])
            ->where('status', 'active');

        if ($key === 'featured') {
            $query->where('is_promoted', true);
        }

        $listings = $query->latest()->take(60)->get()->map(
            fn ($item) => $this->shapeListing($item, $authUser, $userPreference, $myItems)
        );

        return view('pages.items-section', [
            'section'  => $key,
            'listings' => $listings,
            'total'    => $listings->count(),
        ]);
    }

    private function shapeListing(Item $item, $authUser, $userPreference, $myItems): array
    {
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
            $matchScore = $this->matchScorer->score($item, $userPreference, $myItems);
        }

        return [
            'id'          => $item->id,
            'title'       => $item->title,
            'image'       => $primaryImage ? media_url($primaryImage->path) : null,
            'value'       => $item->estimated_value ?? 0,
            'username'    => $item->user->username ?? '',
            'owner'       => $item->user->name ?? '',
            'avatar'      => $initials,
            'avatarColor' => $color,
            'distance'    => null,
            'latitude'    => $item->latitude,
            'longitude'   => $item->longitude,
            'category'    => $item->category,
            'condition'   => ucwords(str_replace('_', ' ', $item->condition ?? '')),
            'desc'        => $item->description ?? '',
            'location'    => $item->location ?? '',
            'wants'       => $item->looking_for ?? '',
            'match'       => $matchScore,
            'rating'      => null,
            'badge'       => $item->created_at->isToday() ? 'New' : '',
            'badgeColor'  => $item->created_at->isToday() ? '#149189' : '',
            'promoted'    => (bool) $item->is_promoted,
            'is_own'      => $authUser ? $item->user_id === $authUser->id : false,
        ];
    }

}
