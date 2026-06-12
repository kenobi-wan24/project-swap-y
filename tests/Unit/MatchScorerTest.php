<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\UserPreference;
use App\Support\MatchScorer;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MatchScorerTest extends TestCase
{
    private MatchScorer $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scorer = new MatchScorer();
    }

    private function item(array $attributes): Item
    {
        return new Item($attributes);
    }

    private function pref(array $attributes): UserPreference
    {
        return new UserPreference($attributes);
    }

    // ── Mutual swap intent ────────────────────────────────────────────────────

    public function test_mutual_intent_scores_full_50(): void
    {
        // They want a camera (I have one); I want an iPhone (they list one).
        $theirs = $this->item([
            'title' => 'iPhone 13 Pro 256GB', 'category' => 'Electronics',
            'looking_for' => 'Camera gear or MacBook', 'estimated_value' => null,
        ]);
        $mine = new Collection([
            $this->item(['title' => 'Sony A7 III', 'category' => 'Photography', 'looking_for' => 'iPhone or tablet']),
        ]);

        // no pref → neutral 15 (category) + 10 (value): 50 + 25 = 75
        $this->assertSame(75, $this->scorer->score($theirs, null, $mine));
    }

    public function test_one_sided_intent_scores_25(): void
    {
        // I want their bookshelf, but they want sneakers I don't have.
        $theirs = $this->item([
            'title' => 'Solid Wood Bookshelf', 'category' => 'Furniture',
            'looking_for' => 'Sneakers size 10', 'estimated_value' => null,
        ]);
        $mine = new Collection([
            $this->item(['title' => 'Mid-Century Armchair', 'category' => 'Furniture', 'looking_for' => 'Bookshelf or coffee table']),
        ]);

        $this->assertSame(25 + 15 + 10, $this->scorer->score($theirs, null, $mine));
    }

    public function test_category_keyword_lexicon_matches_camera_gear_to_photography(): void
    {
        // "camera gear" must match a Photography item even when the title
        // never contains the word "camera" — the old substring matcher missed this.
        $theirs = $this->item([
            'title' => 'iPhone 13 Pro', 'category' => 'Electronics',
            'looking_for' => 'camera gear', 'estimated_value' => null,
        ]);
        $mine = new Collection([
            $this->item(['title' => 'Sony A7 III Full Frame', 'category' => 'Photography', 'looking_for' => null]),
        ]);

        // one-sided (they want mine): 25 + 15 + 10
        $this->assertSame(50, $this->scorer->score($theirs, null, $mine));
    }

    public function test_plural_and_singular_wishes_match(): void
    {
        $theirs = $this->item([
            'title' => 'Canon EF 50mm Lens', 'category' => 'Photography',
            'looking_for' => null, 'estimated_value' => null,
        ]);
        $mine = new Collection([
            $this->item(['title' => 'Tripod', 'category' => 'Photography', 'looking_for' => 'lenses']),
        ]);

        // i want theirs: 25 one-sided
        $this->assertSame(50, $this->scorer->score($theirs, null, $mine));
    }

    public function test_no_false_positive_from_stopwords_or_substrings(): void
    {
        // Old matcher: "for" (≥3 chars) substring-matched "Comfort..." titles,
        // and "art" matched "Smart...". Token matching must not.
        $theirs = $this->item([
            'title' => 'Smart Watch', 'category' => 'Electronics',
            'looking_for' => 'art for the living room', 'estimated_value' => null,
        ]);
        $mine = new Collection([
            $this->item(['title' => 'Comfort Foam Mattress', 'category' => 'Home', 'looking_for' => 'plant stand']),
        ]);

        // no intent in either direction: 0 + 15 + 10
        $this->assertSame(25, $this->scorer->score($theirs, null, $mine));
    }

    // ── Category preference ──────────────────────────────────────────────────

    public function test_preferred_category_adds_30(): void
    {
        $theirs = $this->item([
            'title' => 'Trek Mountain Bike', 'category' => 'Outdoor',
            'looking_for' => null, 'estimated_value' => null,
        ]);
        $pref = $this->pref(['categories' => ['Outdoor', 'Gaming'], 'value_min' => 0, 'value_max' => 1000]);

        // 0 intent + 30 category + 10 value-neutral (item has no value)
        $this->assertSame(40, $this->scorer->score($theirs, $pref, new Collection()));
    }

    public function test_unpreferred_category_adds_nothing(): void
    {
        $theirs = $this->item([
            'title' => 'Trek Mountain Bike', 'category' => 'Outdoor',
            'looking_for' => null, 'estimated_value' => null,
        ]);
        $pref = $this->pref(['categories' => ['Books'], 'value_min' => 0, 'value_max' => 1000]);

        $this->assertSame(10, $this->scorer->score($theirs, $pref, new Collection()));
    }

    public function test_lowercase_legacy_preferences_still_match(): void
    {
        // Older onboarding rows stored lowercase ids like "electronics".
        $theirs = $this->item([
            'title' => 'iPad Pro', 'category' => 'Electronics',
            'looking_for' => null, 'estimated_value' => null,
        ]);
        $pref = $this->pref(['categories' => ['electronics'], 'value_min' => 0, 'value_max' => 1000]);

        $this->assertSame(40, $this->scorer->score($theirs, $pref, new Collection()));
    }

    // ── Value range ──────────────────────────────────────────────────────────

    public function test_value_inside_range_adds_20(): void
    {
        $theirs = $this->item([
            'title' => 'Headphones', 'category' => 'Electronics',
            'looking_for' => null, 'estimated_value' => 700,
        ]);
        $pref = $this->pref(['categories' => [], 'value_min' => 100, 'value_max' => 1000]);

        // 0 intent + 15 neutral category (empty prefs) + 20 value
        $this->assertSame(35, $this->scorer->score($theirs, $pref, new Collection()));
    }

    public function test_value_near_range_adds_10(): void
    {
        $theirs = $this->item([
            'title' => 'Headphones', 'category' => 'Electronics',
            'looking_for' => null, 'estimated_value' => 1150,
        ]);
        // buffer = 20% of 900 = 180 → 1150 ≤ 1180 → near
        $pref = $this->pref(['categories' => [], 'value_min' => 100, 'value_max' => 1000]);

        $this->assertSame(25, $this->scorer->score($theirs, $pref, new Collection()));
    }

    public function test_swapped_min_max_does_not_break_value_scoring(): void
    {
        // Corrupt rows (min > max) from the old slider must not zero the signal.
        $theirs = $this->item([
            'title' => 'Headphones', 'category' => 'Electronics',
            'looking_for' => null, 'estimated_value' => 700,
        ]);
        $pref = $this->pref(['categories' => [], 'value_min' => 1000, 'value_max' => 100]);

        $this->assertSame(35, $this->scorer->score($theirs, $pref, new Collection()));
    }

    // ── Bounds ───────────────────────────────────────────────────────────────

    public function test_perfect_match_caps_at_100(): void
    {
        $theirs = $this->item([
            'title' => 'iPhone 13 Pro', 'category' => 'Electronics',
            'looking_for' => 'camera', 'estimated_value' => 500,
        ]);
        $mine = new Collection([
            $this->item(['title' => 'Canon Camera', 'category' => 'Photography', 'looking_for' => 'iphone']),
        ]);
        $pref = $this->pref(['categories' => ['Electronics'], 'value_min' => 0, 'value_max' => 1000]);

        $this->assertSame(100, $this->scorer->score($theirs, $pref, $mine));
    }

    public function test_guest_like_empty_context_is_baseline(): void
    {
        $theirs = $this->item([
            'title' => 'iPhone 13 Pro', 'category' => 'Electronics',
            'looking_for' => 'camera', 'estimated_value' => 500,
        ]);

        // no pref, no items: 0 + 15 + 10
        $this->assertSame(25, $this->scorer->score($theirs, null, new Collection()));
    }
}
