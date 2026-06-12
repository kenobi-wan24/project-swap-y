<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use App\Support\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function step1(Request $request)
    {
        $user = Auth::user();
        return view('pages.onboarding', compact('user'));
    }

    public function step2(Request $request)
    {
        return $this->step1($request);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'categories'             => ['nullable', 'array'],
            'categories.*'           => ['string', Rule::in(Categories::ITEMS)],
            'value_min'              => ['required', 'integer', 'min:0'],
            'value_max'              => ['required', 'integer', 'min:0', 'gte:value_min'],
            'max_distance'           => ['required', 'integer', 'min:1', 'max:100'],
            'notification_frequency' => ['required', 'in:frequent,balanced,minimal'],
            'intent'                 => ['nullable', 'in:post,explore'],
        ]);

        UserPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return response()->json(['success' => true]);
    }
}