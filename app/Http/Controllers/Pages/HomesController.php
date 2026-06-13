<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Home;
use App\Models\HomeImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomesController extends Controller
{
    /** "See all" sections, mirroring the horizontal rows on the browse page. */
    public const SECTIONS = ['featured', 'popular', 'nearby', 'near-you'];

    private const PALETTE = ['#ED730C', '#14b8a6', '#8b5cf6', '#f59e0b', '#ec4899', '#3b82f6'];

    public function index()
    {
        $homes = Home::with(['images', 'owner'])
            ->where('status', 'active')
            ->latest()
            ->take(30)
            ->get()
            ->map(fn (Home $home) => $this->shapeHome($home));

        return view('pages.homes', compact('homes'));
    }

    /** Full listing for one browse section (featured filters to promoted). */
    public function section(string $key)
    {
        abort_unless(in_array($key, self::SECTIONS, true), 404);

        $query = Home::with(['images', 'owner'])->where('status', 'active');

        if ($key === 'featured') {
            $query->where('is_promoted', true);
        }

        $homes = $query->latest()->take(60)->get()->map(fn (Home $home) => $this->shapeHome($home));

        // The map is an exploration surface — every home with coordinates,
        // not just this section, so panning reveals listings everywhere.
        $mapHomes = Home::with(['images', 'owner'])
            ->where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->take(200)
            ->get()
            ->map(fn (Home $home) => $this->shapeHome($home));

        return view('pages.homes-section', [
            'section' => $key,
            'homes'   => $homes,
            'mapHomes' => $mapHomes,
            'total'   => $homes->count(),
        ]);
    }

    private function shapeHome(Home $home): array
    {
        // primary image first, then the rest
        $images = $home->images
            ->sortByDesc('is_primary')
            ->map(fn ($img) => media_url($img->path))
            ->values()
            ->all();

        $ownerName = $home->owner->name ?? 'Member';
        $initials = collect(explode(' ', trim($ownerName)))
            ->filter()
            ->take(2)
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
            ->implode('');

        return [
            'id'             => $home->id,
            'type'           => $home->type,
            'title'          => $home->title,
            'location'       => $home->location ?: 'Location not set',
            'latitude'       => $home->latitude !== null ? (float) $home->latitude : null,
            'longitude'      => $home->longitude !== null ? (float) $home->longitude : null,
            'city'           => null,
            'area'           => null,
            'distance'       => null,
            'beds'           => $home->beds ?: 'Studio',
            'baths'          => $home->baths ?? 0,
            'sqm'            => $home->sqm ?? 0,
            'value'          => $home->value ?? 0,
            'swap_terms'     => $home->swap_terms,
            'tags'           => $home->tags ?? [],
            'rating'         => 5.0,
            'is_promoted'    => (bool) $home->is_promoted,
            'owner'          => $ownerName,
            'owner_initials' => $initials ?: 'M',
            'owner_color'    => self::PALETTE[$home->id % count(self::PALETTE)],
            'listed_at'      => $home->created_at->diffForHumans(),
            'images'         => $images ?: ['https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80'],
        ];
    }

    public function create()
    {
        return view('pages.home-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => ['required', 'in:Swap,Rent,Sell,Co-living'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'beds'        => ['nullable', 'string', 'max:20'],
            'baths'       => ['nullable', 'integer', 'min:0', 'max:50'],
            'sqm'         => ['nullable', 'integer', 'min:0'],
            'value'       => ['nullable', 'integer', 'min:0'],
            'swap_terms'  => ['nullable', 'string'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['string', 'max:40'],
            'location'    => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'images'      => ['nullable', 'array', 'max:8'],
            'images.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $home = Home::create([
            'user_id'     => Auth::id(),
            'type'        => $validated['type'],
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'beds'        => $validated['beds'] ?? null,
            'baths'       => $validated['baths'] ?? null,
            'sqm'         => $validated['sqm'] ?? null,
            'value'       => $validated['value'] ?? null,
            'swap_terms'  => $validated['swap_terms'] ?? null,
            'tags'        => $validated['tags'] ?? [],
            'location'    => $validated['location'] ?? null,
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'status'      => 'active',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('homes', 'public');
                HomeImage::create([
                    'home_id'    => $home->id,
                    'path'       => $path,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'home'    => $home->load('images'),
        ]);
    }
}