<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        /*
        |----------------------------------------------------------------------
        | TODO (Backend): Replace $services with a real DB query.
        |
        | Accepts query params from Vue/Axios:
        |   ?category=Design+%26+Creative
        |   ?match_only=1   (only show services matching user's active listings)
        |
        | Expected shape per service:
        |   'id'               => int
        |   'title'            => string
        |   'description'      => string
        |   'image'            => string (URL)
        |   'category'         => string  (matches sidebar: 'Design & Creative', etc.)
        |   'tag'              => string  (short tag shown on card image badge)
        |   'provider_initials'=> string  (2-char avatar fallback)
        |   'match_type'       => string  (swap_potential|high_match|direct_match|mutual_interest|verified|local_match)
        |   'match_percent'    => int|null (only used when match_type = swap_potential)
        |   'is_match'         => bool    (true if this provider matches the user's wishlist)
        |
        | Example query:
        |   $services = Listing::where('section', 'services')
        |       ->where('status', 'active')
        |       ->when($request->category, fn($q,$c) => $q->where('subcategory', $c))
        |       ->latest()->paginate(9);
        |----------------------------------------------------------------------
        */

        $services = [
            [
                'id'                => 1,
                'title'             => 'Professional Logo Design',
                'description'       => 'Complete branding package including logo, typography, and brand guidelines...',
                'image'             => 'https://images.unsplash.com/photo-1547658719-da2b51169166?w=600&q=80',
                'category'          => 'Design & Creative',
                'tag'               => 'Design',
                'provider_initials' => 'AL',
                'match_type'        => 'swap_potential',
                'match_percent'     => 98,
                'is_match'          => true,
            ],
            [
                'id'                => 2,
                'title'             => 'React Web Development',
                'description'       => 'Front-end development for landing pages and simple web applications...',
                'image'             => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=600&q=80',
                'category'          => 'Tech & Digital',
                'tag'               => 'Tech',
                'provider_initials' => 'JD',
                'match_type'        => 'high_match',
                'match_percent'     => null,
                'is_match'          => true,
            ],
            [
                'id'                => 3,
                'title'             => 'Math & Physics Tutoring',
                'description'       => 'Experienced tutor for high school and college level mathematics and physics...',
                'image'             => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&q=80',
                'category'          => 'Education & Tutoring',
                'tag'               => 'Education',
                'provider_initials' => 'MK',
                'match_type'        => 'direct_match',
                'match_percent'     => null,
                'is_match'          => true,
            ],
            [
                'id'                => 4,
                'title'             => 'Social Media Strategy',
                'description'       => 'Growth plan and content calendar creation for Instagram, TikTok, and LinkedIn...',
                'image'             => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80',
                'category'          => 'Business',
                'tag'               => 'Business',
                'provider_initials' => 'SR',
                'match_type'        => 'mutual_interest',
                'match_percent'     => null,
                'is_match'          => true,
            ],
            [
                'id'                => 5,
                'title'             => 'Interior Wall Painting',
                'description'       => 'Reliable interior painting for single rooms or small apartments...',
                'image'             => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=600&q=80',
                'category'          => 'Home & Repair',
                'tag'               => 'Home',
                'provider_initials' => 'TC',
                'match_type'        => 'verified',
                'match_percent'     => null,
                'is_match'          => false,
            ],
            [
                'id'                => 6,
                'title'             => 'Pottery Workshop',
                'description'       => '2-hour private session teaching basic wheel-throwing and hand-building techniques...',
                'image'             => 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=600&q=80',
                'category'          => 'Creative',
                'tag'               => 'Creative',
                'provider_initials' => 'LN',
                'match_type'        => 'local_match',
                'match_percent'     => null,
                'is_match'          => false,
            ],
            [
                'id'                => 7,
                'title'             => 'SEO & Content Writing',
                'description'       => 'Keyword research, on-page SEO, and blog content writing for small businesses...',
                'image'             => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=600&q=80',
                'category'          => 'Business',
                'tag'               => 'Business',
                'provider_initials' => 'PW',
                'match_type'        => 'swap_potential',
                'match_percent'     => 84,
                'is_match'          => true,
            ],
            [
                'id'                => 8,
                'title'             => 'Video Editing & Motion',
                'description'       => 'Professional video editing with color grading, sound design and motion graphics...',
                'image'             => 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=600&q=80',
                'category'          => 'Design & Creative',
                'tag'               => 'Design',
                'provider_initials' => 'BM',
                'match_type'        => 'high_match',
                'match_percent'     => null,
                'is_match'          => true,
            ],
            [
                'id'                => 9,
                'title'             => 'Guitar Lessons — Beginner',
                'description'       => 'Acoustic or electric guitar lessons for complete beginners, ages 10 and up...',
                'image'             => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?w=600&q=80',
                'category'          => 'Education & Tutoring',
                'tag'               => 'Education',
                'provider_initials' => 'GR',
                'match_type'        => 'local_match',
                'match_percent'     => null,
                'is_match'          => false,
            ],
        ];

        return view('pages.services', compact('services'));
    }
}