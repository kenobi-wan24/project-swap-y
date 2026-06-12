<?php

namespace App\Support;

use App\Models\Item;
use App\Models\UserPreference;
use Illuminate\Support\Collection;

/**
 * Computes how well a listing matches the viewing user (0–100).
 *
 * Signals and weights:
 *   [50] Mutual swap intent — the owner's "looking for" matches one of my
 *        items AND one of my items' "looking for" matches their listing.
 *        Only one direction matching earns 25 (partial credit).
 *   [30] Category preference — the listing's category is one of the
 *        categories picked during onboarding. Users without saved
 *        preferences get a neutral 15 so scores stay comparable.
 *   [20] Value range — the listing's value sits inside the preferred
 *        range from onboarding (10 when it's within 20% outside the
 *        range, neutral 10 when no preference / no value).
 *
 * Text matching is token-based (not substring) with stopwords removed,
 * singular/plural normalization, and a per-category keyword lexicon so
 * wishes like "camera gear" match a Photography listing even when the
 * title never contains the word "camera".
 */
class MatchScorer
{
    private const MUTUAL_POINTS    = 50;
    private const ONE_SIDED_POINTS = 25;

    private const CATEGORY_POINTS  = 30;
    private const CATEGORY_NEUTRAL = 15;

    private const VALUE_POINTS  = 20;
    private const VALUE_NEAR    = 10;
    private const VALUE_NEUTRAL = 10;

    /** Generic words that carry no swap intent. */
    private const STOPWORDS = [
        'the', 'and', 'for', 'any', 'all', 'with', 'your', 'our', 'this', 'that',
        'new', 'used', 'like', 'open', 'offer', 'offers', 'item', 'items', 'good',
        'quality', 'nice', 'set', 'size', 'pro', 'plus', 'mini', 'max',
    ];

    /** Words in a wish that imply a whole category. Keys follow Categories::ITEMS. */
    private const CATEGORY_KEYWORDS = [
        'Electronics'  => ['electronics', 'phone', 'iphone', 'smartphone', 'tablet', 'ipad', 'laptop', 'macbook', 'monitor', 'headphone', 'earbud', 'speaker', 'keyboard', 'mouse', 'mic', 'microphone', 'gadget', 'tech'],
        'Clothing'     => ['clothing', 'clothes', 'shirt', 'jacket', 'sweater', 'jeans', 'dress', 'pants'],
        'Collectibles' => ['collectible', 'vinyl', 'record', 'antique', 'card', 'figure', 'memorabilia', 'guitar', 'instrument'],
        'Furniture'    => ['furniture', 'chair', 'table', 'desk', 'sofa', 'couch', 'bookshelf', 'shelf', 'shelves', 'armchair', 'daybed'],
        'Books'        => ['book', 'novel', 'library', 'textbook', 'comic'],
        'Outdoor'      => ['outdoor', 'bike', 'bicycle', 'camping', 'tent', 'surfboard', 'kayak', 'fishing', 'hiking'],
        'Gaming'       => ['gaming', 'console', 'playstation', 'xbox', 'nintendo', 'switch', 'ps4', 'ps5', 'controller', 'game', 'games'],
        'Photography'  => ['photography', 'camera', 'lens', 'lenses', 'dslr', 'mirrorless', 'tripod', 'softbox', 'lighting'],
        'Home'         => ['home', 'appliance', 'vacuum', 'purifier', 'lamp', 'rug', 'planter', 'kitchen', 'decor'],
        'Fashion'      => ['fashion', 'sneaker', 'shoe', 'bag', 'hoodie', 'watch', 'cap', 'accessory', 'accessories', 'jewelry'],
    ];

    /**
     * @param Item            $item    the listing being scored
     * @param UserPreference|null $pref the viewer's onboarding preferences
     * @param Collection      $myItems the viewer's own active items
     *                                 (id, title, category, looking_for)
     */
    public function score(Item $item, ?UserPreference $pref, Collection $myItems): int
    {
        $score = 0;

        // ── Signal 1: mutual swap intent (50) ────────────────────────────────
        $theyWantMine = $this->wishMatchesAny($item->looking_for, $myItems);
        $iWantTheirs  = $myItems->contains(
            fn ($mine) => $this->wishMatchesItem($mine->looking_for ?? '', $item->title, $item->category)
        );

        if ($theyWantMine && $iWantTheirs) {
            $score += self::MUTUAL_POINTS;
        } elseif ($theyWantMine || $iWantTheirs) {
            $score += self::ONE_SIDED_POINTS;
        }

        // ── Signal 2: category preference (30) ───────────────────────────────
        $preferred = array_map('strtolower', (array) ($pref->categories ?? []));
        if ($preferred) {
            if (in_array(strtolower($item->category ?? ''), $preferred, true)) {
                $score += self::CATEGORY_POINTS;
            }
        } else {
            $score += self::CATEGORY_NEUTRAL;
        }

        // ── Signal 3: value range (20) ───────────────────────────────────────
        if ($item->estimated_value !== null && $pref) {
            $min = min($pref->value_min ?? 0, $pref->value_max ?? 0);
            $max = max($pref->value_min ?? 0, $pref->value_max ?? 0);
            $buffer = max(($max - $min) * 0.2, 50);

            $value = $item->estimated_value;
            if ($value >= $min && $value <= $max) {
                $score += self::VALUE_POINTS;
            } elseif ($value >= $min - $buffer && $value <= $max + $buffer) {
                $score += self::VALUE_NEAR;
            }
        } else {
            $score += self::VALUE_NEUTRAL;
        }

        return max(0, min(100, $score));
    }

    /** Does a "looking for" wish match any of the given items? */
    private function wishMatchesAny(?string $wish, Collection $items): bool
    {
        if (! $wish || $items->isEmpty()) {
            return false;
        }

        return $items->contains(
            fn ($item) => $this->wishMatchesItem($wish, $item->title ?? '', $item->category ?? '')
        );
    }

    /** Does a "looking for" wish describe an item with this title/category? */
    private function wishMatchesItem(string $wish, string $title, string $category): bool
    {
        $wishTokens = $this->tokenize($wish);
        if (! $wishTokens) {
            return false;
        }

        $titleTokens   = $this->tokenize($title);
        $categoryLower = strtolower($category);
        $lexicon       = array_map(
            'strtolower',
            self::CATEGORY_KEYWORDS[ucfirst($categoryLower)] ?? self::CATEGORY_KEYWORDS[$category] ?? []
        );

        foreach ($wishTokens as $wishToken) {
            // direct word match against the title
            foreach ($titleTokens as $titleToken) {
                if ($this->tokensEqual($wishToken, $titleToken)) {
                    return true;
                }
            }

            // the wish names the category itself, or a word that implies it
            if ($this->tokensEqual($wishToken, $categoryLower)) {
                return true;
            }
            foreach ($lexicon as $keyword) {
                if ($this->tokensEqual($wishToken, $keyword)) {
                    return true;
                }
            }
        }

        return false;
    }

    /** Lowercase word tokens, ≥3 chars, stopwords removed. */
    private function tokenize(string $text): array
    {
        $tokens = preg_split('/[^a-z0-9]+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_filter(
            $tokens,
            fn ($t) => strlen($t) >= 3 && ! in_array($t, self::STOPWORDS, true)
        ));
    }

    /** Equal words, tolerating simple plurals (camera/cameras, lens/lenses). */
    private function tokensEqual(string $a, string $b): bool
    {
        return $a === $b
            || $a === $b . 's' || $b === $a . 's'
            || $a === $b . 'es' || $b === $a . 'es';
    }
}
