<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\BrowseController;
use App\Http\Controllers\Pages\GarageSaleController;
use App\Http\Controllers\Pages\ServicesController;
use App\Http\Controllers\Pages\HowItWorksController;
use App\Http\Controllers\Pages\ItemController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Pages\OnboardingController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\ProfileController;

Route::middleware('guest')->group(function () {  
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
    Route::get('/api/check-username', [AuthController::class, 'checkUsername']);
});   

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public routes
Route::get('/',              [HomeController::class,       'index'])->name('home');
Route::get('/browse',        [BrowseController::class,     'index'])->name('browse');
Route::get('/garage-sale',   [GarageSaleController::class, 'index'])->name('garage-sale');
Route::get('/services',      [ServicesController::class,   'index'])->name('services');
Route::get('/how-it-works',  [HowItWorksController::class, 'index'])->name('how-it-works');
Route::get('/item/{id}',     [ItemController::class,       'show'])->name('item.show');
Route::get('/user/{username}',[ProfileController::class,   'show'])->name('user.profile');
Route::get('/store/{username}',[ProfileController::class,  'store'])->name('user.store');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile',   [ProfileController::class,  'myProfile'])->name('profile');

    Route::get('/onboarding/preferences', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::get('/onboarding/intent',      [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/onboarding/save',       [OnboardingController::class, 'save'])->name('onboarding.save');

    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
});