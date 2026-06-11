<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\BrowseController;
use App\Http\Controllers\Pages\GarageSaleController;
use App\Http\Controllers\Pages\GarageSaleDetailController;
use App\Http\Controllers\Pages\HomesController;
use App\Http\Controllers\Pages\HomeDetailController;
use App\Http\Controllers\Pages\ServicesController;
use App\Http\Controllers\Pages\HowItWorksController;
use App\Http\Controllers\Pages\ItemController;
use App\Http\Controllers\Pages\ListingActionController;
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
Route::get('/', fn() => redirect()->route('browse'))->name('home');
Route::get('/browse',        [BrowseController::class,     'index'])->name('browse');
Route::get('/homes',         [HomesController::class,      'index'])->name('homes');
Route::get('/homes/create',  [HomesController::class,      'create'])->name('homes.create')->middleware('auth');
Route::get('/homes/{id}',    [HomeDetailController::class, 'show'])->name('homes.show');
Route::get('/garage-sale',           [GarageSaleController::class,       'index'])->name('garage-sale');
Route::get('/garage-sale/create',    [GarageSaleController::class,       'create'])->name('garage-sale.create')->middleware('auth');
Route::get('/garage-sale/{id}',      [GarageSaleDetailController::class, 'show'])->name('garage-sale.show');
Route::get('/services',      [ServicesController::class,   'index'])->name('services');
Route::get('/services/create',[ServicesController::class,  'create'])->name('services.create')->middleware('auth');
Route::get('/how-it-works',  [HowItWorksController::class, 'index'])->name('how-it-works');
Route::get('/items/create',  [ItemController::class,       'create'])->name('items.create')->middleware('auth');
Route::get('/item/{id}',     [ItemController::class,       'show'])->name('item.show');
Route::get('/user/{username}',[ProfileController::class,   'show'])->name('user.profile');
Route::get('/store/{username}',[ProfileController::class,  'store'])->name('user.store');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/listings/{type}/{id}/promote', [ListingActionController::class, 'promote'])->name('listings.promote');
    Route::put('/listings/{type}/{id}',          [ListingActionController::class, 'update'])->name('listings.update');
    Route::get('/profile',   [ProfileController::class,  'myProfile'])->name('profile');

    Route::get('/onboarding/preferences', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::get('/onboarding/intent',      [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/onboarding/save',       [OnboardingController::class, 'save'])->name('onboarding.save');

    Route::post('/items',       [ItemController::class,      'store'])->name('items.store');
    Route::post('/garage-sale', [GarageSaleController::class,'store'])->name('garage-sale.store');
    Route::post('/homes',       [HomesController::class,     'store'])->name('homes.store');
    Route::post('/services',    [ServicesController::class,  'store'])->name('services.store');
});