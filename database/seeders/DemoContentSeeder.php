<?php

namespace Database\Seeders;

use App\Models\GarageSale;
use App\Models\GarageSaleItem;
use App\Models\Home;
use App\Models\HomeImage;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    /** Area coordinates used to place listings on the map. */
    private const PLACES = [
        'Lagao, General Santos City'        => [6.1219, 125.1839],
        'Downtown, General Santos City'     => [6.1128, 125.1717],
        'City Heights, General Santos City' => [6.1247, 125.1751],
        'Bula, General Santos City'         => [6.0964, 125.1647],
        'San Isidro, General Santos City'   => [6.1342, 125.1606],
        'Poblacion, Davao City'             => [7.0731, 125.6128],
        'Matina, Davao City'                => [7.0512, 125.5921],
        'Buhangin, Davao City'              => [7.1075, 125.6300],
        'Lanang, Davao City'                => [7.1099, 125.6549],
        'Poblacion, Polomolok'              => [6.2210, 125.0641],
    ];

    public function run(): void
    {
        $users = $this->seedUsers();
        $this->seedItems($users);
        $this->seedServices($users);
        $this->seedHomes($users);
        $this->seedGarageSales($users);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Users
    // ─────────────────────────────────────────────────────────────────────────
    private function seedUsers(): array
    {
        $rows = [
            ['Sarah Lim',      'sarah_lim',      'Lagao, General Santos City',        ['Electronics', 'Books'],        ['Audio', 'Electronics', 'Books']],
            ['Julian Mercado', 'julian_design',  'Matina, Davao City',                ['Photography', 'Electronics'],  ['Photography', 'Electronics']],
            ['David Co',       'david_codes',    'Buhangin, Davao City',              ['Gaming', 'Electronics'],       ['Gaming', 'Electronics']],
            ['Mia Ramos',      'mia_creates',    'Poblacion, Davao City',             ['Fashion', 'Home'],             ['Fashion', 'Clothing', 'Home']],
            ['Leo Bautista',   'leo_books',      'Downtown, General Santos City',     ['Books', 'Collectibles'],       ['Books', 'Collectibles']],
            ['Nina Torres',    'nina_trades',    'Lagao, General Santos City',        ['Home', 'Furniture'],           ['Home', 'Furniture']],
            ['Carla Dizon',    'carla_davao',    'Poblacion, Davao City',             ['Fashion', 'Clothing'],         ['Fashion', 'Clothing']],
            ['Marco Punzalan', 'marco_palengke', 'Downtown, General Santos City',     ['Home', 'Outdoor'],             ['Home', 'Outdoor']],
            ['Joy Santos',     'joy_swaps',      'Matina, Davao City',                ['Books', 'Home'],               ['Books', 'Home']],
            ['Ben Chua',       'ben_collects',   'Buhangin, Davao City',              ['Gaming', 'Collectibles'],      ['Gaming', 'Collectibles', 'Electronics']],
            ['Tanya Mendoza',  'tanya_market',   'City Heights, General Santos City', ['Clothing', 'Fashion'],         ['Clothing', 'Fashion']],
            ['Alex Reyes',     'alex_rides',     'Poblacion, Polomolok',              ['Outdoor', 'Gaming'],           ['Outdoor', 'Photography']],
        ];

        $users = [];
        foreach ($rows as $i => [$name, $username, $location, , $prefCategories]) {
            $user = User::updateOrCreate(
                ['email' => $username . '@swapy.test'],
                [
                    'name'     => $name,
                    'username' => $username,
                    'password' => 'password',
                    'location' => $location,
                ]
            );

            UserPreference::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'categories'             => $prefCategories,
                    'value_min'              => 50,
                    'value_max'              => 3000 + ($i % 3) * 1000,
                    'max_distance'           => 25,
                    'notification_frequency' => 'balanced',
                    'intent'                 => $i % 2 === 0 ? 'post' : 'explore',
                ]
            );

            $users[$username] = $user;
        }

        return $users;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Items
    // ─────────────────────────────────────────────────────────────────────────
    private function seedItems(array $users): void
    {
        $img = fn (string $id, int $w = 800) => "https://images.unsplash.com/{$id}?w={$w}&q=80";

        $items = [
            // user, title, category, condition, value, looking_for, place, promoted, image, description
            ['sarah_lim',      'Sony WH-1000XM4 Headphones',      'Electronics',  'like_new', 700,  'Graphic tablet or iPad',          'Lagao, General Santos City',        false, 'photo-1505740420928-5e560c06d30e', 'Industry-leading noise cancelling. Complete with case, cable, and original box.'],
            ['sarah_lim',      'Bose SoundLink Mini II',          'Electronics',  'good',     480,  'Earbuds or small speaker',        'Lagao, General Santos City',        false, 'photo-1608043152269-423dbba4e7e1', 'Rich sound in a compact body. Battery still lasts around 9 hours.'],
            ['sarah_lim',      'Design Book Library (12 books)',  'Books',        'like_new', 250,  'Programming books',               'Lagao, General Santos City',        false, 'photo-1524995997946-a1c2e315a42f', 'Twelve design classics — typography, UX, and branding. Barely opened.'],
            ['julian_design',  'Sony A7 III with Kit Lens',       'Photography',  'like_new', 4500, 'Canon R5 or cinema lenses',       'Matina, Davao City',                true,  'photo-1516035069371-29a1b244cc32', 'Only 3k shutter count. Kit lens, two batteries, and charger included.'],
            ['julian_design',  'Godox LED Light Panel',           'Photography',  'good',     360,  'Ring light or softbox',           'Matina, Davao City',                false, 'photo-1598300042247-d088f8ab3a91', 'Bi-color LED panel with barn doors and stand. Great for product shoots.'],
            ['julian_design',  'Canon EF 50mm f/1.8 Lens',        'Photography',  'like_new', 550,  'Wide angle lens',                 'Matina, Davao City',                false, 'photo-1510127034890-ba27508e9f1c', 'The classic nifty fifty. Crisp glass, no fungus, caps and pouch included.'],
            ['david_codes',    'ASUS ROG Strix Gaming Laptop',    'Gaming',       'good',     3500, 'iPhone or MacBook',               'Buhangin, Davao City',              true,  'photo-1603302576837-37561b2e2302', 'RTX 3070, 16GB RAM, 1TB SSD. Runs everything on high settings.'],
            ['david_codes',    'Dell UltraSharp 27" 4K Monitor',  'Electronics',  'good',     1000, 'Gaming monitor 144Hz',            'Buhangin, Davao City',              false, 'photo-1527443224154-c4a3942d3acf', 'Stunning 4K IPS panel, factory calibrated. Includes USB-C cable.'],
            ['david_codes',    'Mechanical Keyboard TKL',         'Electronics',  'like_new', 350,  'Wireless mouse or mic',           'Buhangin, Davao City',              false, 'photo-1618384887929-16ec33fab9ef', 'Hot-swappable switches, PBT keycaps. Typed on for two months only.'],
            ['mia_creates',    'Supreme Box Logo Hoodie',         'Fashion',      'like_new', 1100, 'Sneakers size 10 or caps',        'Poblacion, Davao City',             false, 'photo-1620799140408-edc6dcb6d633', 'Size L, worn twice. No stains or defects. Rare colourway.'],
            ['mia_creates',    'Artisan Ceramic Dinner Set',      'Home',         'new',      280,  'Outdoor planters or rug',         'Poblacion, Davao City',             false, 'photo-1565193566173-7a0ee3dbe261', 'Handmade 16-piece stoneware set. Still boxed — unwanted gift.'],
            ['leo_books',      'Vinyl Record Collection (50+)',   'Collectibles', 'good',     600,  'Cassette player or turntable',    'Downtown, General Santos City',     false, 'photo-1603048588665-791ca8aea617', 'Mixed genres, 60s to 90s. Rare OPM pressings and classic albums.'],
            ['leo_books',      'Atomic Habits + 5 Self-Help Books', 'Books',      'good',     90,   'Any psychology books',            'Downtown, General Santos City',     false, 'photo-1544947950-fa07a98d237f',    'Six bestsellers in great shape. Perfect starter stack for the year.'],
            ['nina_trades',    'Mid-Century Armchair',            'Furniture',    'good',     850,  'Bookshelf or coffee table',       'Lagao, General Santos City',        true,  'photo-1567538096630-e0c55bd6374c', 'Solid teak frame, newly reupholstered cushions. Pickup preferred.'],
            ['nina_trades',    'Solid Wood Bookshelf',            'Furniture',    'like_new', 520,  'Armchair or desk',                'Lagao, General Santos City',        false, 'photo-1555041469-a586c61ea9bc',    'Five shelves of solid acacia. No wobble, no scratches.'],
            ['nina_trades',    'Standing Desk Frame',             'Home',         'new',      800,  'Office chair',                    'Lagao, General Santos City',        false, 'photo-1593642632559-0c6d3fc62b89', 'Dual-motor sit-stand frame, still sealed in box. Tops not included.'],
            ['carla_davao',    'Uniqlo Cashmere Sweater',         'Clothing',     'like_new', 180,  'Linen shirts',                    'Poblacion, Davao City',             false, 'photo-1434389677669-e08b4cac3105', 'Size M, 100% cashmere in oatmeal. Worn a handful of times.'],
            ['carla_davao',    'Leather Camera Bag',              'Fashion',      'good',     380,  'Hard-shell suitcase',             'Poblacion, Davao City',             false, 'photo-1548036328-c9fa89d128fa',    'Full-grain leather messenger fits a body and two lenses.'],
            ['marco_palengke', 'Dyson V11 Cordless Vacuum',       'Home',         'good',     900,  'Air purifier',                    'Downtown, General Santos City',     false, 'photo-1558618666-fcd25c85cd64',    'Powerful cordless vacuum, all attachments included. Works perfectly.'],
            ['marco_palengke', 'Camping Gear Bundle',             'Outdoor',      'good',     950,  'Fishing equipment',               'Downtown, General Santos City',     false, 'photo-1504280390367-361c6d9f38f4', '4-person tent, two sleeping bags, stove, and lantern. Used twice.'],
            ['joy_swaps',      'Thinking, Fast and Slow (Mint)',  'Books',        'new',      120,  'Finance or psychology books',     'Matina, Davao City',                false, 'photo-1589998059171-988d887df646', 'Hardbound, mint condition. Duplicate copy from a book fair haul.'],
            ['ben_collects',   'Nintendo Switch OLED',            'Gaming',       'good',     1500, 'PS4 Pro or games bundle',         'Buhangin, Davao City',              true,  'photo-1578303512597-81e6cc155b3e', 'White OLED model with dock, two sets of Joy-Cons, and three games.'],
            ['ben_collects',   'Razer Blade 15',                  'Gaming',       'good',     3800, 'MacBook Pro',                     'Buhangin, Davao City',              false, 'photo-1593642634524-b40b5baae6bb', 'RTX 3060, 240Hz screen. Light gaming use, repasted this year.'],
            ['tanya_market',   'iPhone 13 Pro 256GB',             'Electronics',  'like_new', 2800, 'Camera gear or MacBook',          'City Heights, General Santos City', true,  'photo-1591337676887-a217a6970a8a', 'Sierra Blue, 89% battery health, factory unlocked, complete box.'],
            ['tanya_market',   'iPad Pro 12.9" M2',               'Electronics',  'like_new', 2900, 'MacBook Air M2',                  'City Heights, General Santos City', true,  'photo-1544244015-0df4b3ffc6b0',    'Barely used, with Apple Pencil 2 and Magic Keyboard case.'],
            ['alex_rides',     'Trek Marlin Mountain Bike 29"',   'Outdoor',      'good',     1900, 'Surfboard or kayak',              'Poblacion, Polomolok',              true,  'photo-1485965120184-e220f721d03e', 'Carbon fork, Shimano Deore groupset. Trail-ready, freshly tuned.'],
            ['alex_rides',     'Rode VideoMicro',                 'Electronics',  'like_new', 380,  'Lavalier mic',                    'Poblacion, Polomolok',              false, 'photo-1598550476439-6847785fcea6', 'Compact on-camera mic with deadcat and shock mount.'],
            ['sarah_lim',      'Fujifilm X-T30 II',               'Photography',  'good',     2200, 'Fuji XF lenses',                  'Lagao, General Santos City',        false, 'photo-1502920917128-1aa500764cbd', 'Lovely retro body with 15-45mm kit lens. Looking for XF 35mm.'],
            ['joy_swaps',      'Acoustic Guitar Yamaha F310',     'Collectibles', 'good',     320,  'Keyboard or ukulele',             'Matina, Davao City',                false, 'photo-1510915361894-db8b60106cb1', 'Beginner-friendly acoustic with fresh strings, bag included.'],
        ];

        foreach ($items as $i => [$username, $title, $category, $condition, $value, $wants, $place, $promoted, $photo, $description]) {
            $user = $users[$username];
            [$lat, $lng] = $this->jitter($place, $i);

            $item = Item::firstOrCreate(
                ['user_id' => $user->id, 'title' => $title],
                [
                    'description'     => $description,
                    'estimated_value' => $value,
                    'category'        => $category,
                    'condition'       => $condition,
                    'location'        => $place,
                    'looking_for'     => $wants,
                    'swap_conditions' => [],
                    'status'          => 'active',
                    'latitude'        => $lat,
                    'longitude'       => $lng,
                    'is_promoted'     => $promoted,
                    'created_at'      => now()->subHours(($i * 9) % 160),
                    'updated_at'      => now()->subHours(($i * 5) % 48),
                ]
            );

            if ($item->wasRecentlyCreated) {
                ItemImage::create([
                    'item_id'    => $item->id,
                    'path'       => $img($photo),
                    'is_primary' => true,
                ]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Services
    // ─────────────────────────────────────────────────────────────────────────
    private function seedServices(array $users): void
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=800&q=80";

        $services = [
            // user, title, category, rate, rate_type, delivery, swap_for, tags, place, promoted, image, description
            ['julian_design',  'Logo & Brand Identity Design',     'Design & Creative',    2500, 'Per project', 'Remote',    'Camera gear or lighting equipment',  ['Logo', 'Branding', 'Style guide'],      'Matina, Davao City',                true,  'photo-1561070791-2526d30994b5', 'Complete brand package: logo, color palette, typography, and brand guide.'],
            ['julian_design',  'Photography & Photo Editing',      'Creative',             1500, 'Per session', 'In-person', 'Studio props or backdrops',          ['Events', 'Products', 'Retouching'],     'Matina, Davao City',                true,  'photo-1516035069371-29a1b244cc32', 'Product, lifestyle, and event photography with quick turnaround edits.'],
            ['david_codes',    'Laravel & Vue Web Development',    'Tech & Digital',       800,  'Per hour',    'Remote',    'PC parts or mechanical keyboards',   ['Laravel', 'Vue', 'REST APIs'],          'Buhangin, Davao City',              true,  'photo-1555066931-4365d14431b9', 'End-to-end web apps with Laravel, Vue, REST APIs, and deployment pipelines.'],
            ['leo_books',      'Math & Science Tutoring (K-12)',   'Education & Tutoring', 350,  'Per hour',    'Both',      'Books or school supplies',           ['Algebra', 'Physics', 'Chemistry'],      'Downtown, General Santos City',     false, 'photo-1503676260728-1c00da094a0b', 'Patient, effective tutoring for students struggling with STEM subjects.'],
            ['joy_swaps',      'English & IELTS Coaching',         'Education & Tutoring', 500,  'Per hour',    'Remote',    'Children\'s books or board games',   ['IELTS', 'Business English'],            'Matina, Davao City',                false, 'photo-1456513080510-7bf3a84b82f8', 'Conversational English, business writing, and IELTS/TOEFL preparation.'],
            ['marco_palengke', 'Home Painting & Finishing',        'Home & Repair',        4500, 'Per project', 'In-person', 'Power tools or building materials',  ['Interior', 'Exterior', 'Waterproofing'],'Downtown, General Santos City',     false, 'photo-1562259949-e8e7689d7828', 'Interior and exterior painting, wallpaper, and decorative finishes.'],
            ['marco_palengke', 'Aircon Cleaning & Repair',         'Home & Repair',        900,  'Per unit',    'In-person', 'Kitchen appliances',                 ['Split-type', 'Window-type'],            'Downtown, General Santos City',     false, 'photo-1621905251189-08b45d6a269e', 'Deep cleaning, freon top-up, and minor repairs for all aircon types.'],
            ['mia_creates',    'Social Media Content Creation',    'Creative',             3000, 'Per month',   'Remote',    'Fashion pieces or accessories',      ['Reels', 'Captions', 'Scheduling'],      'Poblacion, Davao City',             false, 'photo-1611162617213-7d7a39e9b1d7', 'Monthly content calendar, captions, graphics, and scheduling for IG/FB.'],
            ['tanya_market',   'Bookkeeping & Tax Filing',         'Business',             1200, 'Per month',   'Both',      'Office furniture or gadgets',        ['BIR filing', 'Payroll', 'Reports'],     'City Heights, General Santos City', false, 'photo-1554224155-6726b3ff858f', 'Monthly bookkeeping, BIR filing, and financial statement preparation.'],
        ];

        foreach ($services as $i => [$username, $title, $category, $rate, $rateType, $delivery, $swapFor, $tags, $place, $promoted, $photo, $description]) {
            $user = $users[$username];
            [$lat, $lng] = $this->jitter($place, $i + 40);

            $service = Service::firstOrCreate(
                ['user_id' => $user->id, 'title' => $title],
                [
                    'category'    => $category,
                    'description' => $description,
                    'rate'        => $rate,
                    'rate_type'   => $rateType,
                    'delivery'    => $delivery,
                    'swap_for'    => $swapFor,
                    'tags'        => $tags,
                    'location'    => $place,
                    'latitude'    => $lat,
                    'longitude'   => $lng,
                    'status'      => 'active',
                    'is_promoted' => $promoted,
                    'created_at'  => now()->subHours(($i * 11) % 140),
                    'updated_at'  => now()->subHours(($i * 6) % 36),
                ]
            );

            if ($service->wasRecentlyCreated) {
                ServiceImage::create([
                    'service_id' => $service->id,
                    'path'       => $img($photo),
                    'is_primary' => true,
                ]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Homes
    // ─────────────────────────────────────────────────────────────────────────
    private function seedHomes(array $users): void
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=900&q=80";

        $homes = [
            // user, type, title, beds, baths, sqm, value, swap_terms, tags, place, promoted, images, description
            ['nina_trades',    'Rent',      'Cozy Studio near Gensan Downtown',  'Studio', 1, 28,  9000,    null,                                          ['Furnished', 'Near market', 'City center'],        'Downtown, General Santos City',     true,  ['photo-1522708323590-d24dbb6b0267', 'photo-1502672260266-1c1ef2d93688'], 'Bright, fully furnished studio steps from the public market and city plaza.'],
            ['carla_davao',    'Rent',      '2BR Condo with Pool View',          '2',      2, 68,  20000,   null,                                          ['Semi-furnished', 'With parking', 'Pool'],         'Lanang, Davao City',                false, ['photo-1493809842364-78817add7ffb'],                                     'Corner unit overlooking the amenity deck. One parking slot included.'],
            ['ben_collects',   'Sell',      'Family Home with Garden',           '3',      2, 120, 4800000, null,                                          ['With garden', 'Quiet village', 'Near airport'],   'Buhangin, Davao City',              false, ['photo-1568605114967-8130f3a36994', 'photo-1570129477492-45c003edd2be'], 'Single-detached home in a gated subdivision, ten minutes from the airport.'],
            ['tanya_market',   'Swap',      '1BR Loft near City Heights',        '1',      1, 42,  15000,   'Open to swap with a unit in Davao City',      ['Loft-style', 'Balcony', 'Fully furnished'],       'City Heights, General Santos City', true,  ['photo-1502672260266-1c1ef2d93688'],                                     'Double-height loft with balcony. Looking to relocate to Davao for work.'],
            ['mia_creates',    'Co-living', 'Private Room in Co-living Hub',     '1',      1, 18,  6500,    'Open to skill-swap for partial rent',         ['Fast WiFi', 'All utilities', 'Cowork space'],     'Poblacion, Davao City',             false, ['photo-1555854877-bab0e564b8d5'],                                        'Private room in a creative co-living space. Shared kitchen and coworking area.'],
            ['marco_palengke', 'Sell',      'Townhouse near SM Gensan',          '3',      2, 95,  3500000, 'Open to partial swap + cash top-up',          ['With garage', 'Corner lot', 'Renovated'],         'Lagao, General Santos City',        false, ['photo-1570129477492-45c003edd2be'],                                     'Newly renovated two-storey townhouse, walking distance to SM City Gensan.'],
        ];

        foreach ($homes as $i => [$username, $type, $title, $beds, $baths, $sqm, $value, $swapTerms, $tags, $place, $promoted, $photos, $description]) {
            $user = $users[$username];
            [$lat, $lng] = $this->jitter($place, $i + 70);

            $home = Home::firstOrCreate(
                ['user_id' => $user->id, 'title' => $title],
                [
                    'type'        => $type,
                    'description' => $description,
                    'beds'        => $beds,
                    'baths'       => $baths,
                    'sqm'         => $sqm,
                    'value'       => $value,
                    'swap_terms'  => $swapTerms,
                    'tags'        => $tags,
                    'location'    => $place,
                    'latitude'    => $lat,
                    'longitude'   => $lng,
                    'status'      => 'active',
                    'is_promoted' => $promoted,
                    'created_at'  => now()->subHours(($i * 13) % 120),
                    'updated_at'  => now()->subHours(($i * 7) % 30),
                ]
            );

            if ($home->wasRecentlyCreated) {
                foreach ($photos as $idx => $photo) {
                    HomeImage::create([
                        'home_id'    => $home->id,
                        'path'       => $img($photo),
                        'is_primary' => $idx === 0,
                    ]);
                }
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Garage sales
    // ─────────────────────────────────────────────────────────────────────────
    private function seedGarageSales(array $users): void
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?w=900&q=80";

        $sales = [
            [
                'user'        => 'sarah_lim',
                'title'       => "Sarah's Audio & Tech Garage Sale",
                'description' => 'Audiophile, photographer, and avid reader. Everything listed is well-loved and honestly described. Open to creative swaps — just reach out!',
                'place'       => 'Lagao, General Santos City',
                'promoted'    => true,
                'cover'       => 'photo-1505740420928-5e560c06d30e',
                'items'       => [
                    ['Sony WH-1000XM5',         'Audio',       'like_new', 8500,  'IEM or DAC',          'photo-1505740420928-5e560c06d30e', 'Open to swap for quality IEM or portable DAC'],
                    ['Marshall Stanmore II',    'Audio',       'good',     9500,  'Vinyl turntable',     'photo-1608043152269-423dbba4e7e1', null],
                    ['Mechanical Keyboard 65%', 'Electronics', 'new',      3200,  'Wireless mouse',      'photo-1618384887929-16ec33fab9ef', null],
                    ['Kindle Paperwhite',       'Electronics', 'good',     4200,  'Books or book vouchers', 'photo-1544947950-fa07a98d237f', null],
                    ['Rode PodMic',             'Audio',       'like_new', 4800,  'Audio interface',     'photo-1598550476439-6847785fcea6', 'Prefer Focusrite Scarlett or similar'],
                ],
            ],
            [
                'user'        => 'julian_design',
                'title'       => 'Weekend Declutter: Camera Gear',
                'description' => 'Clearing shelf space in the studio. All gear tested and working — happy to demo any item before swapping.',
                'place'       => 'Matina, Davao City',
                'promoted'    => true,
                'cover'       => 'photo-1516035069371-29a1b244cc32',
                'items'       => [
                    ['Canon EOS M50 Body',      'Cameras',     'good',     18000, 'Mirrorless lens',     'photo-1516035069371-29a1b244cc32', 'Looking for EF-M or RF glass'],
                    ['Manfrotto Tripod',        'Accessories', 'good',     4500,  'Camera sling bag',    'photo-1502920917128-1aa500764cbd', null],
                    ['Godox Speedlite',         'Accessories', 'like_new', 3800,  'LED panels',          'photo-1598300042247-d088f8ab3a91', null],
                    ['Leather Camera Strap',    'Accessories', 'new',      1200,  'SD cards',            'photo-1548036328-c9fa89d128fa',    null],
                ],
            ],
            [
                'user'        => 'nina_trades',
                'title'       => 'Moving-Out Sale: Furniture & Home',
                'description' => 'Relocating to a smaller place — solid furniture and home pieces need a new owner. Pickup in Lagao preferred.',
                'place'       => 'Lagao, General Santos City',
                'promoted'    => false,
                'cover'       => 'photo-1555041469-a586c61ea9bc',
                'items'       => [
                    ['Mid-Century Coffee Table', 'Furniture', 'good',     5500,  'Bookshelf',           'photo-1567538096630-e0c55bd6374c', null],
                    ['Acacia Dining Set (4)',    'Furniture', 'good',     12000, 'Sofa or daybed',      'photo-1555041469-a586c61ea9bc',    'Open to partial swap plus cash'],
                    ['Ceramic Planter Set',      'Home',      'new',      1800,  'Indoor plants',       'photo-1565193566173-7a0ee3dbe261', null],
                    ['Floor Lamp (Brass)',       'Home',      'like_new', 2400,  'Wall shelves',        'photo-1513506003901-1e6a229e2d15', null],
                    ['Area Rug 160x230',         'Home',      'good',     3000,  'Curtains or blinds',  'photo-1522708323590-d24dbb6b0267', null],
                ],
            ],
            [
                'user'        => 'ben_collects',
                'title'       => "Gamer's Garage Sale",
                'description' => 'Console and PC gear from years of collecting. Everything works — bring a game to test if you like.',
                'place'       => 'Buhangin, Davao City',
                'promoted'    => false,
                'cover'       => 'photo-1587202372583-49330a15584d',
                'items'       => [
                    ['PS4 Pro 1TB',             'Gaming',      'good',     12000, 'Nintendo games',      'photo-1587202372583-49330a15584d', null],
                    ['Xbox Controller (2x)',    'Gaming',      'like_new', 2800,  'PC game codes',       'photo-1592840496694-26d035b52b48', null],
                    ['Gaming Chair',            'Furniture',   'good',     6500,  'Office chair',        'photo-1598550476439-6847785fcea6', null],
                    ['144Hz Gaming Monitor',    'Electronics', 'good',     8500,  '4K monitor',          'photo-1527443224154-c4a3942d3acf', 'Will add cash for a good 4K IPS'],
                    ['Retro Game Cartridges',   'Collectibles','fair',     3500,  'Vinyl records',       'photo-1603048588665-791ca8aea617', null],
                ],
            ],
            [
                'user'        => 'carla_davao',
                'title'       => 'Closet Cleanout: Fashion & Vintage',
                'description' => 'Curated pieces from my closet — designer, streetwear, and vintage finds. All authentic, all cared for.',
                'place'       => 'Poblacion, Davao City',
                'promoted'    => false,
                'cover'       => 'photo-1445205170230-053b83016050',
                'items'       => [
                    ['Vintage Levi\'s 501',     'Vintage',     'good',     2200,  'Denim jacket',        'photo-1542272604-787c3835535d',    null],
                    ['Coach Shoulder Bag',      'Bags',        'like_new', 7500,  'Crossbody bags',      'photo-1548036328-c9fa89d128fa',    'Authentic, with dust bag and card'],
                    ['Nike Air Force 1 (US 8)', 'Shoes',       'good',     3500,  'Adidas Samba',        'photo-1600185365926-3a2ce3cdb9eb', null],
                    ['Silk Scarf Collection',   'Accessories', 'new',      1500,  'Statement earrings',  'photo-1434389677669-e08b4cac3105', null],
                ],
            ],
        ];

        foreach ($sales as $s => $row) {
            $user = $users[$row['user']];
            [$lat, $lng] = $this->jitter($row['place'], $s + 90);

            $sale = GarageSale::firstOrCreate(
                ['user_id' => $user->id, 'title' => $row['title']],
                [
                    'description' => $row['description'],
                    'cover_image' => $img($row['cover']),
                    'location'    => $row['place'],
                    'latitude'    => $lat,
                    'longitude'   => $lng,
                    'status'      => 'active',
                    'is_promoted' => $row['promoted'],
                    'created_at'  => now()->subHours(($s * 17) % 100),
                    'updated_at'  => now()->subHours(($s * 4) % 20),
                ]
            );

            if ($sale->wasRecentlyCreated) {
                foreach ($row['items'] as [$title, $category, $condition, $value, $wants, $photo, $swapTerms]) {
                    GarageSaleItem::create([
                        'garage_sale_id' => $sale->id,
                        'title'          => $title,
                        'category'       => $category,
                        'condition'      => $condition,
                        'value'          => $value,
                        'image_path'     => $img($photo),
                        'wants'          => $wants,
                        'swap_terms'     => $swapTerms,
                    ]);
                }
            }
        }
    }

    /** Deterministic small offset so listings in one area don't stack on the map. */
    private function jitter(string $place, int $seed): array
    {
        [$lat, $lng] = self::PLACES[$place];

        return [
            round($lat + ((($seed * 13) % 21) - 10) * 0.0009, 7),
            round($lng + ((($seed * 7) % 21) - 10) * 0.0009, 7),
        ];
    }
}
