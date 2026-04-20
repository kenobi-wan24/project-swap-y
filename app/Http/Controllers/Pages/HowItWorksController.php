<?php
// filepath: app/Http/Controllers/Pages/HowItWorksController.php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HowItWorksController extends Controller
{
    public function index()
    {
        /*
        |----------------------------------------------------------------------
        | TODO (Backend): Replace with DB queries when tutorial CMS is ready.
        |
        | Featured shape:
        |   'title'       => string
        |   'description' => string
        |   'image'       => string (URL)
        |   'duration'    => string  (e.g. '12:45')
        |   'category'    => string  (badge label)
        |
        | Guides shape:
        |   'id'          => int
        |   'title'       => string
        |   'thumbnail'   => string (URL)
        |   'duration'    => string
        |   'views'       => string  (e.g. '1.2k')
        |   'posted'      => string  (e.g. '2 days ago')
        |   'category'    => string  (matches filter tabs)
        |----------------------------------------------------------------------
        */

        $featured = [
            'title'       => 'The Ultimate Guide to Your First Successful Swap',
            'description' => 'Learn how to set up your profile, list your first item, and initiate a swap that works for everyone.',
            'image'       => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=900&q=80',
            'duration'    => '12:45',
            'category'    => 'Getting Started',
        ];

        $guides = [
            [
                'id'        => 1,
                'title'     => 'Understanding Multi-Leg Swaps',
                'thumbnail' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80',
                'duration'  => '3:45',
                'views'     => '1.2k',
                'posted'    => '2 days ago',
                'category'  => 'Multi-Leg',
            ],
            [
                'id'        => 2,
                'title'     => 'Shipping Best Practices',
                'thumbnail' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400&q=80',
                'duration'  => '4:12',
                'views'     => '856',
                'posted'    => '5 days ago',
                'category'  => 'How To Swap',
            ],
            [
                'id'        => 3,
                'title'     => 'Communication Guidelines',
                'thumbnail' => 'https://images.unsplash.com/photo-1573497620053-ea5300f94f21?w=400&q=80',
                'duration'  => '2:50',
                'views'     => '3.4k',
                'posted'    => '1 week ago',
                'category'  => 'How To Swap',
            ],
            [
                'id'        => 4,
                'title'     => 'Listing Services & Creations',
                'thumbnail' => 'https://images.unsplash.com/photo-1542744095-fcf48d80b0fd?w=400&q=80',
                'duration'  => '6:15',
                'views'     => '2.1k',
                'posted'    => '2 weeks ago',
                'category'  => 'Getting Started',
            ],
            [
                'id'        => 5,
                'title'     => 'How to Verify Your Account',
                'thumbnail' => 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?w=400&q=80',
                'duration'  => '3:20',
                'views'     => '4.7k',
                'posted'    => '3 weeks ago',
                'category'  => 'Safety',
            ],
            [
                'id'        => 6,
                'title'     => 'Swap Dispute Resolution',
                'thumbnail' => 'https://images.unsplash.com/photo-1453728013993-6d66e9c9123a?w=400&q=80',
                'duration'  => '5:08',
                'views'     => '980',
                'posted'    => '1 month ago',
                'category'  => 'Safety',
            ],
            [
                'id'        => 7,
                'title'     => 'Setting Your Wish List Filters',
                'thumbnail' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=400&q=80',
                'duration'  => '2:33',
                'views'     => '1.8k',
                'posted'    => '1 month ago',
                'category'  => 'Getting Started',
            ],
            [
                'id'        => 8,
                'title'     => '3-Way Swap Chain Walkthrough',
                'thumbnail' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&q=80',
                'duration'  => '7:02',
                'views'     => '5.3k',
                'posted'    => '2 months ago',
                'category'  => 'Multi-Leg',
            ],
        ];

        return view('pages.how-it-works', compact('featured', 'guides'));
    }
}