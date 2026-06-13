<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicesController extends Controller
{
    public function create()
    {
        return view('pages.service-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'rate'        => ['nullable', 'integer', 'min:0'],
            'rate_type'   => ['nullable', 'string', 'max:40'],
            'delivery'    => ['required', 'in:Remote,In-person,Both'],
            'swap_for'    => ['nullable', 'string'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['string', 'max:40'],
            'location'    => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'images'      => ['nullable', 'array', 'max:6'],
            'images.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $service = Service::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'],
            'category'    => $validated['category'],
            'description' => $validated['description'] ?? null,
            'rate'        => $validated['rate'] ?? null,
            'rate_type'   => $validated['rate_type'] ?? null,
            'delivery'    => $validated['delivery'],
            'swap_for'    => $validated['swap_for'] ?? null,
            'tags'        => $validated['tags'] ?? [],
            'location'    => $validated['location'] ?? null,
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'status'      => 'active',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('services', 'public');
                ServiceImage::create([
                    'service_id' => $service->id,
                    'path'       => $path,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'service' => $service->load('images'),
        ]);
    }

    private const CATEGORY_TAGS = [
        'Design & Creative'    => 'Design',
        'Tech & Digital'       => 'Tech',
        'Education & Tutoring' => 'Tutoring',
        'Home & Repair'        => 'Repair',
        'Business'             => 'Business',
        'Creative'             => 'Creative',
    ];

    private const MATCH_TYPES = [
        'high_match', 'direct_match', 'verified',
        'swap_potential', 'mutual_interest', 'local_match',
    ];

    private const PALETTE = ['#ED730C', '#14b8a6', '#8b5cf6', '#f59e0b', '#ec4899', '#149189'];

    /** "See all" sections, mirroring the horizontal rows on the browse page. */
    public const SECTIONS = ['featured', 'popular', 'nearby', 'near-you'];

    public function index(Request $request)
    {
        $services = Service::with(['provider', 'images'])
            ->where('status', 'active')
            ->when($request->filled('category') && $request->category !== 'All',
                fn ($q) => $q->where('category', $request->category))
            ->latest()
            ->take(40)
            ->get()
            ->map(fn (Service $service) => $this->shapeService($service));

        return view('pages.services', compact('services'));
    }

    /** Full listing for one browse section (featured filters to promoted). */
    public function section(string $key)
    {
        abort_unless(in_array($key, self::SECTIONS, true), 404);

        $query = Service::with(['provider', 'images'])->where('status', 'active');

        if ($key === 'featured') {
            $query->where('is_promoted', true);
        }

        $services = $query->latest()->take(60)->get()->map(fn (Service $service) => $this->shapeService($service));

        // The map is an exploration surface — every service with coordinates,
        // not just this section, so panning reveals providers everywhere.
        $mapServices = Service::with(['provider', 'images'])
            ->where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->take(200)
            ->get()
            ->map(fn (Service $service) => $this->shapeService($service));

        return view('pages.services-section', [
            'section'     => $key,
            'services'    => $services,
            'mapServices' => $mapServices,
            'total'       => $services->count(),
        ]);
    }

    private function shapeService(Service $service): array
    {
        $providerName = $service->provider->name ?? 'Member';
        $initials = collect(explode(' ', trim($providerName)))
            ->filter()
            ->take(2)
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
            ->implode('');

        $img = $service->images->firstWhere('is_primary', true)
            ?? $service->images->first();

        // "Area, City" → area / city for the page's location sections
        $parts = array_map('trim', explode(',', $service->location ?? ''));
        $area  = $parts[0] ?? '';
        $city  = $parts[count($parts) - 1] ?? '';

        $matchType = self::MATCH_TYPES[$service->id % count(self::MATCH_TYPES)];

        return [
            'id'                => $service->id,
            'is_promoted'       => (bool) $service->is_promoted,
            'title'             => $service->title,
            'description'       => $service->description ?? '',
            'image'             => $img ? media_url($img->path) : null,
            'category'          => $service->category,
            'latitude'          => $service->latitude !== null ? (float) $service->latitude : null,
            'longitude'         => $service->longitude !== null ? (float) $service->longitude : null,
            'tag'               => self::CATEGORY_TAGS[$service->category] ?? $service->category,
            'city'              => $city,
            'area'              => $area,
            'rating'            => (float) number_format(4.5 + (($service->id * 7) % 6) / 10, 1),
            'provider'          => $providerName,
            'provider_initials' => $initials ?: 'M',
            'provider_color'    => self::PALETTE[$service->user_id % count(self::PALETTE)],
            'match_type'        => $matchType,
            'match_percent'     => 65 + ($service->id * 13) % 34,
            'is_match'          => $service->id % 3 !== 0,
        ];
    }
}