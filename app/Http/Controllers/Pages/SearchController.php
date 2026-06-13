<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\GarageSale;
use App\Models\Home;
use App\Models\Item;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Universal search across every listing type.
 *
 * Matching runs in three tiers:
 *   1. Tokenized MySQL FULLTEXT (boolean mode, prefix `*`, AND across words)
 *      with relevance ranking — the primary path.
 *   2. Tokenized LIKE fallback for queries too short for FULLTEXT (MySQL
 *      ignores tokens under innodb_ft_min_token_size, default 3 chars).
 *   3. A Levenshtein fuzzy pass that only fires when the first two tiers
 *      return very little, so typos ("camra" → "camera") still find results.
 *
 * The `location` box is always an additional AND filter on top of the query.
 */
class SearchController extends Controller
{
    private const MIN_FT_LEN     = 3;  // MySQL FULLTEXT minimum token length
    private const FUZZY_MIN_HITS = 3;  // run the fuzzy pass below this many results
    private const FUZZY_SCAN_CAP = 200;

    public function index(Request $request)
    {
        $q   = trim((string) $request->query('q', ''));
        $loc = trim((string) $request->query('location', ''));

        $results = [];

        if ($q !== '' || $loc !== '') {
            $tokens = $this->tokenize($q);

            $results = array_merge(
                $this->searchItems($tokens, $loc),
                $this->searchHomes($tokens, $loc),
                $this->searchGarageSales($tokens, $loc),
                $this->searchServices($tokens, $loc),
            );

            // Tier 3 — typo tolerance, only when the keyword tiers came up short.
            if ($q !== '' && count($results) < self::FUZZY_MIN_HITS) {
                $seen = [];
                foreach ($results as $r) {
                    $seen[$r['type'] . ':' . $r['id']] = true;
                }
                $results = array_merge($results, $this->fuzzyFallback($tokens, $loc, $seen));
            }
        }

        return view('pages.search', [
            'q'        => $q,
            'location' => $loc,
            'results'  => $results,
        ]);
    }

    // ── Query helpers ─────────────────────────────────────────────────────────

    /** Split into lowercase alphanumeric tokens (unicode-aware). */
    private function tokenize(string $q): array
    {
        $parts = preg_split('/\s+/u', mb_strtolower($q), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_filter(array_map(
            fn ($t) => preg_replace('/[^\p{L}\p{N}]+/u', '', $t),
            $parts
        )));
    }

    private function ftTokens(array $tokens): array
    {
        return array_values(array_filter($tokens, fn ($t) => mb_strlen($t) >= self::MIN_FT_LEN));
    }

    /** "+vintage* +camera*" — every word required, prefix-matched. */
    private function booleanExpr(array $ftTokens): string
    {
        return implode(' ', array_map(fn ($t) => "+{$t}*", $ftTokens));
    }

    private function withLocation(Builder $query, string $loc): Builder
    {
        if ($loc !== '') {
            $query->where('location', 'like', "%{$loc}%");
        }

        return $query;
    }

    private static function coord($value): ?float
    {
        return $value !== null ? (float) $value : null;
    }

    // ── Per-type search ───────────────────────────────────────────────────────

    private function searchItems(array $tokens, string $loc): array
    {
        $ft    = $this->ftTokens($tokens);
        $query = Item::with(['user', 'images'])->where('status', 'active');

        if ($ft) {
            $expr    = 'MATCH(items.title, items.description, items.category, items.location, items.looking_for) AGAINST(? IN BOOLEAN MODE)';
            $boolean = $this->booleanExpr($ft);
            $query->selectRaw("items.*, {$expr} AS relevance", [$boolean])
                  ->whereRaw($expr, [$boolean])
                  ->orderByDesc('relevance');
        } elseif ($tokens) {
            $this->likeAllTokens($query, $tokens, ['title', 'description', 'category', 'location', 'looking_for']);
            $query->latest();
        } else {
            $query->latest();
        }

        return $this->withLocation($query, $loc)->take(40)->get()
            ->map(fn (Item $i) => $this->shapeItem($i))->all();
    }

    private function searchHomes(array $tokens, string $loc): array
    {
        $ft    = $this->ftTokens($tokens);
        $query = Home::with(['images', 'owner'])->where('status', 'active');

        if ($ft) {
            $expr    = 'MATCH(homes.title, homes.description, homes.location) AGAINST(? IN BOOLEAN MODE)';
            $boolean = $this->booleanExpr($ft);
            $query->selectRaw("homes.*, {$expr} AS relevance", [$boolean])
                  ->where(function (Builder $w) use ($expr, $boolean, $tokens) {
                      $w->whereRaw($expr, [$boolean]);
                      // "rent" / "swap" / "sell" should still surface homes by type.
                      foreach ($tokens as $t) {
                          $w->orWhere('homes.type', 'like', "%{$t}%");
                      }
                  })
                  ->orderByDesc('relevance');
        } elseif ($tokens) {
            $this->likeAllTokens($query, $tokens, ['title', 'description', 'location', 'type']);
            $query->latest();
        } else {
            $query->latest();
        }

        return $this->withLocation($query, $loc)->take(40)->get()
            ->map(fn (Home $h) => $this->shapeHome($h))->all();
    }

    private function searchGarageSales(array $tokens, string $loc): array
    {
        $ft    = $this->ftTokens($tokens);
        $query = GarageSale::with(['seller', 'items'])->where('status', 'active');

        if ($ft) {
            $expr      = 'MATCH(garage_sales.title, garage_sales.description, garage_sales.location) AGAINST(? IN BOOLEAN MODE)';
            $childExpr = 'MATCH(title, category) AGAINST(? IN BOOLEAN MODE)';
            $boolean   = $this->booleanExpr($ft);
            $query->selectRaw("garage_sales.*, {$expr} AS relevance", [$boolean])
                  ->where(function (Builder $w) use ($expr, $childExpr, $boolean, $tokens) {
                      $w->whereRaw($expr, [$boolean])
                        ->orWhereHas('items', fn (Builder $iq) => $iq->whereRaw($childExpr, [$boolean]));
                      foreach ($tokens as $t) {
                          $w->orWhereHas('seller', fn (Builder $sq) => $sq->where('name', 'like', "%{$t}%"));
                      }
                  })
                  ->orderByDesc('relevance');
        } elseif ($tokens) {
            foreach ($tokens as $t) {
                $query->where(function (Builder $w) use ($t) {
                    $w->where('title', 'like', "%{$t}%")
                      ->orWhere('description', 'like', "%{$t}%")
                      ->orWhere('location', 'like', "%{$t}%")
                      ->orWhereHas('items', fn (Builder $iq) => $iq->where('title', 'like', "%{$t}%")->orWhere('category', 'like', "%{$t}%"))
                      ->orWhereHas('seller', fn (Builder $sq) => $sq->where('name', 'like', "%{$t}%"));
                });
            }
            $query->latest('updated_at');
        } else {
            $query->latest('updated_at');
        }

        return $this->withLocation($query, $loc)->take(40)->get()
            ->map(fn (GarageSale $s) => $this->shapeSale($s))->all();
    }

    private function searchServices(array $tokens, string $loc): array
    {
        $ft    = $this->ftTokens($tokens);
        $query = Service::with(['provider', 'images'])->where('status', 'active');

        if ($ft) {
            $expr    = 'MATCH(services.title, services.description, services.category, services.location) AGAINST(? IN BOOLEAN MODE)';
            $boolean = $this->booleanExpr($ft);
            $query->selectRaw("services.*, {$expr} AS relevance", [$boolean])
                  ->whereRaw($expr, [$boolean])
                  ->orderByDesc('relevance');
        } elseif ($tokens) {
            $this->likeAllTokens($query, $tokens, ['title', 'description', 'category', 'location']);
            $query->latest();
        } else {
            $query->latest();
        }

        return $this->withLocation($query, $loc)->take(40)->get()
            ->map(fn (Service $sv) => $this->shapeService($sv))->all();
    }

    /** AND across tokens, OR across the given columns. */
    private function likeAllTokens(Builder $query, array $tokens, array $columns): void
    {
        foreach ($tokens as $t) {
            $query->where(function (Builder $w) use ($t, $columns) {
                foreach ($columns as $c) {
                    $w->orWhere($c, 'like', "%{$t}%");
                }
            });
        }
    }

    // ── Tier 3: fuzzy / typo tolerance ────────────────────────────────────────

    private function fuzzyFallback(array $tokens, string $loc, array $seen): array
    {
        $ft = $this->ftTokens($tokens);
        if (! $ft) {
            return [];
        }

        $out = [];

        $items = $this->withLocation(Item::with(['user', 'images'])->where('status', 'active'), $loc)
            ->latest()->take(self::FUZZY_SCAN_CAP)->get();
        foreach ($items as $i) {
            if (isset($seen['item:' . $i->id])) continue;
            if ($this->fuzzyHit($ft, $i->title . ' ' . $i->category . ' ' . $i->looking_for)) {
                $out[] = $this->shapeItem($i, 0.0);
            }
        }

        $homes = $this->withLocation(Home::with(['images', 'owner'])->where('status', 'active'), $loc)
            ->latest()->take(self::FUZZY_SCAN_CAP)->get();
        foreach ($homes as $h) {
            if (isset($seen['home:' . $h->id])) continue;
            if ($this->fuzzyHit($ft, $h->title . ' ' . $h->type)) {
                $out[] = $this->shapeHome($h, 0.0);
            }
        }

        $sales = $this->withLocation(GarageSale::with(['seller', 'items'])->where('status', 'active'), $loc)
            ->latest('updated_at')->take(self::FUZZY_SCAN_CAP)->get();
        foreach ($sales as $s) {
            if (isset($seen['garage-sale:' . $s->id])) continue;
            $hay = $s->title . ' ' . optional($s->seller)->name . ' ' . $s->items->pluck('title')->implode(' ');
            if ($this->fuzzyHit($ft, $hay)) {
                $out[] = $this->shapeSale($s, 0.0);
            }
        }

        $services = $this->withLocation(Service::with(['provider', 'images'])->where('status', 'active'), $loc)
            ->latest()->take(self::FUZZY_SCAN_CAP)->get();
        foreach ($services as $sv) {
            if (isset($seen['service:' . $sv->id])) continue;
            if ($this->fuzzyHit($ft, $sv->title . ' ' . $sv->category)) {
                $out[] = $this->shapeService($sv, 0.0);
            }
        }

        return $out;
    }

    /** True only when EVERY query token is within a small edit distance of some
     *  word in $text (AND semantics — keeps the rescue tier precise). */
    private function fuzzyHit(array $tokens, ?string $text): bool
    {
        $words = $this->tokenize((string) $text);
        if (! $words) {
            return false;
        }

        foreach ($tokens as $t) {
            $maxLev  = mb_strlen($t) <= 4 ? 1 : 2;
            $matched = false;
            foreach ($words as $w) {
                if ($t === $w || levenshtein($t, $w) <= $maxLev) {
                    $matched = true;
                    break;
                }
            }
            if (! $matched) {
                return false;
            }
        }

        return true;
    }

    // ── Shaping (unified, type-tagged) ────────────────────────────────────────

    private function rel($model): float
    {
        return round((float) ($model->relevance ?? 0), 4);
    }

    private function shapeItem(Item $i, ?float $score = null): array
    {
        $img = $i->images->firstWhere('is_primary', true) ?? $i->images->first();

        return [
            'type'        => 'item',
            'id'          => $i->id,
            'title'       => $i->title,
            'subtitle'    => 'Swap for ' . ($i->looking_for ?: 'open to offers'),
            'category'    => $i->category,
            'image'       => $img ? media_url($img->path) : null,
            'value'       => $i->estimated_value ?? 0,
            'location'    => $i->location ?: '',
            'latitude'    => self::coord($i->latitude),
            'longitude'   => self::coord($i->longitude),
            'url'         => '/item/' . $i->id,
            'is_promoted' => (bool) $i->is_promoted,
            'score'       => $score ?? $this->rel($i),
        ];
    }

    private function shapeHome(Home $h, ?float $score = null): array
    {
        $img  = $h->images->sortByDesc('is_primary')->first();
        $beds = $h->beds ?: 'Studio';

        return [
            'type'        => 'home',
            'id'          => $h->id,
            'title'       => $h->title,
            'subtitle'    => ($beds === 'Studio' ? 'Studio' : $beds . ' bed') . ' · ' . ($h->baths ?? 0) . ' bath',
            'category'    => $h->type,
            'home_type'   => $h->type,
            'image'       => $img ? media_url($img->path) : null,
            'value'       => $h->value ?? 0,
            'location'    => $h->location ?: '',
            'latitude'    => self::coord($h->latitude),
            'longitude'   => self::coord($h->longitude),
            'url'         => '/homes/' . $h->id,
            'is_promoted' => (bool) $h->is_promoted,
            'score'       => $score ?? $this->rel($h),
        ];
    }

    private function shapeSale(GarageSale $s, ?float $score = null): array
    {
        $sellerName = $s->seller->name ?? 'Member';
        $username   = $s->seller->username ?? 'member' . $s->user_id;
        $cover      = media_url($s->cover_image) ?? media_url($s->items->first()?->image_path);

        return [
            'type'        => 'garage-sale',
            'id'          => $s->id,
            'title'       => $s->title ?: ($sellerName . "'s Garage Sale"),
            'subtitle'    => $sellerName . "'s garage sale",
            'category'    => 'Garage Sale',
            'image'       => $cover,
            'item_count'  => $s->items->count(),
            'location'    => $s->location ?: '',
            'latitude'    => self::coord($s->latitude),
            'longitude'   => self::coord($s->longitude),
            'url'         => '/store/' . $username,
            'is_promoted' => (bool) $s->is_promoted,
            'score'       => $score ?? $this->rel($s),
        ];
    }

    private function shapeService(Service $sv, ?float $score = null): array
    {
        $img = $sv->images->firstWhere('is_primary', true) ?? $sv->images->first();

        return [
            'type'        => 'service',
            'id'          => $sv->id,
            'title'       => $sv->title,
            'subtitle'    => $sv->provider->name ?? 'Provider',
            'category'    => $sv->category,
            'image'       => $img ? media_url($img->path) : null,
            'location'    => $sv->location ?: '',
            'latitude'    => self::coord($sv->latitude),
            'longitude'   => self::coord($sv->longitude),
            'url'         => '/services/' . $sv->id,
            'is_promoted' => (bool) $sv->is_promoted,
            'score'       => $score ?? $this->rel($sv),
        ];
    }
}
