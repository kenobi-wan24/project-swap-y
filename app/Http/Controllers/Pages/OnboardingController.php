<?php
// filepath: app/Http/Controllers/Pages/OnboardingController.php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OnboardingController extends Controller
{
    /**
     * Both onboarding steps are handled by a single Vue component.
     * Step 1 = /onboarding/preferences
     * Step 2 = handled client-side (Vue switches steps internally)
     *
     * TODO (Backend): Replace Session::get('fake_user') with Auth::user()
     */
    public function step1(Request $request)
    {
        // Fake user from session — swap for Auth::user() when DB is ready
        $user = Session::get('fake_user', [
            'id'       => 1,
            'name'     => 'Guest',
            'username' => 'guest',
            'email'    => '',
            'avatar'   => null,
        ]);

        return view('pages.onboarding', compact('user'));
    }

    /**
     * Step 2 is rendered client-side by the same Vue component.
     * This route is only used if you want a hard URL for step 2.
     */
    public function step2(Request $request)
    {
        return $this->step1($request);
    }

    /**
     * Save onboarding preferences.
     * TODO (Backend): Persist to user_preferences table.
     *
     * Expected POST body:
     *   categories[]   string[]
     *   value_min      int
     *   value_max      int
     *   max_distance   int
     *   notification   string (frequent|balanced|minimal)
     *   intent         string (post|explore)
     */
    public function save(Request $request)
    {
        // TODO (Backend): UserPreference::updateOrCreate(['user_id' => Auth::id()], [...])
        return response()->json(['success' => true]);
    }
}