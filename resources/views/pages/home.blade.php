
@extends('layouts.guest')
@section('title', 'SWAPY - Swap Anything. Zero Cash Needed.')

@section('content')

{{-- =========================================================
    HERO SECTION - Adjusted card positions & fixed images
========================================================= --}}
<section class="relative flex items-center overflow-hidden py-20">
    
    {{-- Multi-layer animated background with blur circles --}}
    <div class="absolute inset-0">
        {{-- Decorative blur circle overlays (behind everything) --}}
        <div class="absolute -top-20 -right-20 w-96 h-96 lg:w-[28rem] lg:h-[28rem] bg-blue-100/40 backdrop-blur-3xl rounded-full opacity-90 pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 lg:w-96 lg:h-96 bg-gradient-to-br from-orange-100/50 via-teal-100/30 to-blue-100/40 backdrop-blur-3xl rounded-full opacity-50 pointer-events-none animate-pulse-slow"></div>
        
        {{-- Fading background image --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-5 lg:opacity-10" 
             style="background-image: linear-gradient(rgba(255,255,255,0.8), rgba(255,255,255,0.2)), url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=2000&q=80')">
        </div>
        
        {{-- Base gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-br from-orange-100/80 via-white/95 to-teal-100/80 backdrop-blur-sm"></div>

        {{-- Animated overlay pattern --}}
        <div class="absolute inset-0 opacity-20 animate-pulse-slow">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_80%,rgba(251,146,60,0.15),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(20,184,166,0.15),transparent_50%)] animate-pulse [animation-delay:1s]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_40%_40%,rgba(59,130,246,0.1),transparent_50%)] animate-pulse [animation-delay:2s]"></div>
        </div>
        
        {{-- Floating geometric shapes --}}
        <div class="absolute -top-32 -right-32 w-64 h-64 lg:w-80 lg:h-80 bg-gradient-to-br from-primary/10 to-orange-500/10 rounded-full blur-xl animate-float opacity-30"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 lg:w-96 lg:h-96 bg-gradient-to-br from-secondary/10 to-teal-500/10 rounded-full blur-3xl animate-float-slow opacity-20 [animation-delay:3s]"></div>
        <div class="absolute top-1/2 left-8 lg:left-16 w-32 h-32 lg:w-40 lg:h-40 bg-gradient-to-r from-blue-500/5 to-primary/5 rounded-2xl blur-xl animate-bob opacity-40 [animation-delay:1.5s]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-20 items-center">

            {{-- Left: Clean Copy --}}
            <div class="text-center lg:text-left max-w-lg lg:max-w-none space-y-8 lg:space-y-10">
                <div class="inline-flex items-center bg-white/90 backdrop-blur-xl border border-white/60 shadow-lg text-primary font-semibold px-5 py-2.5 rounded-2xl text-sm max-w-max mx-auto lg:mx-0">
                    Join 50K+ swappers worldwide
                    <div class="w-2 h-2 bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full ml-2 animate-ping"></div>
                </div>
                
                <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black leading-tight tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 bg-clip-text">
                    Swap <span class="bg-gradient-to-r from-primary via-orange-500 to-secondary bg-[length:200%_40%] bg-clip-text text-transparent animate-gradient-flow">Anything.</span>
                    <br>
                    Zero Cash <span class="bg-gradient-to-r from-primary via-orange-500 to-secondary bg-[length:200%_40%] bg-clip-text text-transparent animate-gradient-flow">Needed.</span>
                </h1>
                
                <p class="text-lg lg:text-xl text-gray-700 leading-relaxed max-w-md lg:max-w-lg mx-auto lg:mx-0">
                    From smartphones to real estate. AI-powered matching connects you instantly with swappers worldwide. 
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto lg:mx-0">
                    <a href="/register"
                       class="group relative flex-1 inline-flex items-center justify-center bg-gradient-to-r from-primary via-orange-600 to-orange-500 hover:from-orange-500 hover:via-orange-400 hover:to-orange-400 text-white font-bold px-8 py-4 rounded-2xl transition-all duration-500 shadow-xl hover:shadow-2xl hover:-translate-y-1 text-base overflow-hidden">
                        <span class="relative z-10 flex items-center">Start Swapping</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                        <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent -skew-x-12 -translate-x-[100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                    </a>
                    <a href="/browse"
                       class="flex-1 inline-flex items-center justify-center border-2 border-white/50 hover:border-primary/70 bg-white/90 backdrop-blur-xl hover:bg-white text-heading font-bold px-8 py-4 rounded-2xl hover:shadow-xl hover:-translate-y-0.5 transition-all duration-400 text-base shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Browse Items
                    </a>
                </div>
            </div>

            {{-- Right: Enhanced 3D floating card stack - ADJUSTED POSITIONS --}}
            <div class="relative w-full lg:h-96 xl:h-[28rem] mx-auto lg:mx-0 hidden lg:block">
                <div class="absolute inset-0 w-full h-full group">
                    
                    {{-- Back card #3 - MOVED LOWER for better visibility --}}
                    <div class="absolute top-20 right-6 w-52 h-64 lg:w-72 lg:h-80 bg-gradient-to-br from-gray-800/95 to-gray-900/90 backdrop-blur-xl rounded-3xl rotate-6 shadow-2xl overflow-hidden ring-2 ring-black/20 transition-all duration-1000 animate-float-card-1 z-10">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=90" alt="Smartphone" class="w-full h-full object-cover hover:scale-110 transition-transform duration-700" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent/0"></div>
                    </div>
                    
                    {{-- Middle card #2 - FIXED IMAGE & adjusted position --}}
                    <div class="absolute top-12 right-24 w-44 h-56 lg:w-60 lg:h-68 bg-gradient-to-br from-gray-700/90 to-gray-800/90 backdrop-blur-xl rounded-3xl rotate-2 shadow-2xl overflow-hidden ring-2 ring-white/40 transition-all duration-1000 animate-float-card-2 z-20">
                        <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&q=90" alt="Laptop" class="w-full h-full object-cover hover:scale-110 transition-transform duration-700" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-r from-teal-500/30 to-transparent/50"></div>
                    </div>
                    
                    {{-- Front card #1 - Main focus --}}
                    <div class="absolute bottom-2 left-2 w-60 h-72 lg:w-80 lg:h-88 bg-gradient-to-br from-teal-600/95 via-teal-500/90 to-emerald-600/95 backdrop-blur-xl rounded-3xl -rotate-3 shadow-3xl overflow-hidden ring-4 ring-teal-400/50 transition-all duration-1000 animate-float-card-3 z-30">
                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=700&q=90" alt="Luxury watch" class="w-full h-full object-cover hover:scale-110 transition-transform duration-700" loading="lazy">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent h-28"></div>
                    </div>

                    {{-- Glow overlay --}}
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-primary/20 via-transparent to-secondary/20 opacity-0 group-hover:opacity-100 transition-all duration-700 blur-xl"></div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Tailwind CSS animations (add to your CSS or use @layer utilities) --}}
<style>
@keyframes float-card-1 {
    0%, 100% { transform: translateY(0px) rotate(6deg); }
    50% { transform: translateY(-12px) rotate(6deg); }
}

@keyframes float-card-2 {
    0%, 100% { transform: translateY(0px) rotate(2deg); }
    50% { transform: translateY(-8px) rotate(2deg); }
}

@keyframes float-card-3 {
    0%, 100% { transform: translateY(0px) rotate(-3deg); }
    50% { transform: translateY(-16px) rotate(-3deg); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-10px) rotate(2deg); }
    66% { transform: translateY(-20px) rotate(-2deg); }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-15px) scale(1.05); }
}

@keyframes bob {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

@keyframes gradient-flow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes slide-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.2; }
    50% { opacity: 0.4; }
}

.animate-float { animation: float 6s ease-in-out infinite; }
.animate-float-slow { animation: float-slow 8s ease-in-out infinite; }
.animate-bob { animation: bob 4s ease-in-out infinite; }
.animate-gradient-flow { animation: gradient-flow 3s ease-in-out infinite; }
.animate-gradient-x { animation: gradient-flow 2.5s ease-in-out infinite; }
.animate-slide-in-up { animation: slide-in-up 0.8s ease-out forwards; }
.animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
.animate-float-card-1 { animation: float-card-1 4s ease-in-out infinite; }
.animate-float-card-2 { animation: float-card-2 5s ease-in-out infinite reverse; }
.animate-float-card-3 { animation: float-card-3 4.5s ease-in-out infinite; }

.delay-500 { animation-delay: 0.5s; }
.delay-1000 { animation-delay: 1s; }
.delay-1500 { animation-delay: 1.5s; }
.delay-2000 { animation-delay: 2s; }
.delay-3000 { animation-delay: 3s; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

{{-- =========================================================
    TRUST SECTION - New addition
========================================================= --}}
<section class="py-20 bg-white/80 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 items-center text-center md:text-left">
            <div>
                <div class="text-4xl font-black text-primary mb-2">50K+</div>
                <div class="text-sm text-gray-600 font-medium">Happy Swappers</div>
            </div>
            <div>
                <div class="text-4xl font-black text-secondary mb-2">10K+</div>
                <div class="text-sm text-gray-600 font-medium">Active Items</div>
            </div>
            <div>
                <div class="text-4xl font-black text-orange-600 mb-2">$2.5M+</div>
                <div class="text-sm text-gray-600 font-medium">Value Swapped</div>
            </div>
            <div>
                <div class="text-4xl font-black text-teal-600 mb-2">99.8%</div>
                <div class="text-sm text-gray-600 font-medium">Success Rate</div>
            </div>
        </div>
    </div>
</section>


{{-- =========================================================
    HOW IT WORKS - Clean design with primary color blur overlay
========================================================= --}}
<section class="relative py-28 overflow-hidden">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-20">
            <h2 class="text-4xl lg:text-5xl font-black text-heading mb-4 leading-tight">
                How It <span class="text-primary font-black bg-gradient-to-r from-primary/20 to-secondary/20 bg-clip-text">Works</span>
            </h2>
            <div class="w-24 h-1 bg-gradient-to-r from-primary/60 to-secondary/60 mx-auto mb-8 rounded-full shadow-sm"></div>
            <span class="text-sm font-semibold tracking-widest text-gray-500 uppercase">3 Simple Steps</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            {{-- Step 1 --}}
            <div class="group relative bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-lg hover:shadow-xl hover:shadow-primary/20 hover:-translate-y-3 transition-all duration-500 border border-white/50 hover:border-primary/30">
                <div class="absolute -left-3 top-8 w-12 h-12 rounded-2xl bg-gradient-to-r from-orange-500 to-primary text-white font-black text-lg flex items-center justify-center shadow-md">
                    01
                </div>
                <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center mb-6 ml-10 shadow-sm group-hover:bg-primary group-hover:shadow-md transition-all duration-300">
                    <svg class="w-6 h-6 text-primary group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl lg:text-2xl font-bold text-heading mb-4 leading-tight">List Your Item</h3>
                <p class="text-gray-600 text-base leading-relaxed">
                    Upload crystal-clear photos and details. Our AI suggests optimal pricing and categories.
                </p>
            </div>

            {{-- Step 2 --}}
            <div class="group relative bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-lg hover:shadow-xl hover:shadow-secondary/20 hover:-translate-y-3 transition-all duration-500 border border-white/50 hover:border-secondary/30 lg:ml-12">
                <div class="absolute -left-3 top-8 w-12 h-12 rounded-2xl bg-gradient-to-r from-teal-500 to-secondary text-white font-black text-lg flex items-center justify-center shadow-md">
                    02
                </div>
                <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center mb-6 ml-10 shadow-sm group-hover:bg-secondary group-hover:shadow-md transition-all duration-300">
                    <svg class="w-6 h-6 text-secondary group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <h3 class="text-xl lg:text-2xl font-bold text-heading mb-4 leading-tight">Get Perfect Matches</h3>
                <p class="text-gray-600 text-base leading-relaxed">
                    AI-powered matching finds swappers who want exactly what you have. Instant notifications.
                </p>
            </div>

            {{-- Step 3 --}}
            <div class="group relative bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-lg hover:shadow-xl hover:shadow-emerald/20 hover:-translate-y-3 transition-all duration-500 border border-white/50 hover:border-emerald/30">
                <div class="absolute -left-3 top-8 w-12 h-12 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-black text-lg flex items-center justify-center shadow-md">
                    03
                </div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mb-6 ml-10 shadow-sm group-hover:bg-emerald-500 group-hover:shadow-md transition-all duration-300">
                    <svg class="w-6 h-6 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl lg:text-2xl font-bold text-heading mb-4 leading-tight">Swap & Enjoy</h3>
                <p class="text-gray-600 text-base leading-relaxed">
                    Confirm, ship with our prepaid labels, and enjoy your new treasure. Done in days.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================
    FEATURED CREATIONS - With primary color blur overlay
========================================================= --}}
<section class="relative py-28 overflow-hidden">
    {{-- Primary color blur overlay background --}}
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-white/90 to-secondary/5 backdrop-blur-sm"></div>
        <div class="absolute -top-40 -right-40 w-[40rem] h-[40rem] bg-gradient-to-br from-primary/20 to-orange-500/10 rounded-full blur-3xl opacity-40 pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-[36rem] h-[36rem] bg-gradient-to-br from-secondary/20 to-teal-500/10 rounded-full blur-3xl opacity-50 pointer-events-none animate-pulse-slow"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-20">
            <h2 class="text-4xl lg:text-5xl font-black text-heading mb-4">Featured <span class="text-secondary">Items</span></h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Fresh items added in the last 24 hours</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($featuredItems as $index => $item)
            <a href="/item/{{ $item['id'] }}" class="group relative block bg-white/90 backdrop-blur-xl shadow-md hover:shadow-xl hover:shadow-primary/20 rounded-3xl overflow-hidden transition-all duration-500 hover:-translate-y-2 border border-white/50 hover:border-primary/30">
                
                {{-- Image - FLUSH to top edges, rounded-t-2xl --}}
                <div class="aspect-[4/3] overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-2xl group-hover:scale-[1.02] transition-transform duration-500">
                    <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                         class="w-full h-full object-cover hover:scale-110 transition-transform duration-700">
                </div>

                {{-- Card Body --}}
                <div class="p-6 pb-8">
                    {{-- Category Badge --}}
                    <div class="inline-block bg-orange-50 text-orange-800 text-xs font-semibold px-3 py-1.5 rounded-full mb-3">
                        {{ $item['category'] ?? 'Electronics' }}
                    </div>

                    {{-- Title --}}
                    <h3 class="text-2xl font-black text-heading leading-tight mb-3 line-clamp-2 group-hover:text-primary transition-colors">
                        {{ $item['title'] }}
                    </h3>

                    {{-- Est. Value --}}
                    <div class="text-orange-600 font-bold text-lg mb-4">
                        Est. Value: ${{ $item['value'] }}
                    </div>

                    {{-- Wants --}}
                    <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                        Looking to swap for: <span class="font-semibold text-gray-900">{{ $item['wants'] }}</span>
                    </p>

                    {{-- Bottom row: Location + Rating --}}
                    <div class="flex items-center justify-between">
                        {{-- Location --}}
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $item['location'] ?? 'New York, NY' }}
                        </div>

                        {{-- Star Rating --}}
                        <div class="flex items-center gap-1 text-sm font-semibold text-orange-500">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            5.0
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- =========================================================
    PRICING SECTION
========================================================= --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14">
            <h2 class="text-4xl font-bold text-heading mb-3">Choose Your Plan</h2>
            <p class="text-gray-500 text-base max-w-xl mx-auto">
                Flexible plans for every type of swapper.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">

            {{-- ---- STARTER ---- --}}
            <div class="relative flex flex-col bg-gray-50 border border-gray-200 rounded-3xl p-8">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-heading mb-1">Starter</h3>
                    <p class="text-xs text-gray-400 mb-4">6-month free trial</p>
                    <div class="flex items-end gap-1">
                        <span class="text-4xl font-bold text-heading">$0</span>
                        <span class="text-gray-400 text-sm mb-1">/mo</span>
                    </div>
                </div>

                <ul class="space-y-3 flex-1 mb-8">
                    @foreach ([
                        ['ok', 'Browse all listings'],
                        ['ok', 'List items up to $800'],
                        ['ok', '5 photos per item'],
                        ['ok', '15 trades per month'],
                        ['no', 'No real estate access'],
                        ['no', 'No priority matching'],
                    ] as [$type, $text])
                    <li class="flex items-center gap-3 text-sm">
                        @if ($type === 'ok')
                            <svg class="w-4 h-4 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-heading">{{ $text }}</span>
                        @else
                            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-400">{{ $text }}</span>
                        @endif
                    </li>
                    @endforeach
                </ul>

                <a href="/register"
                   class="block text-center border border-primary text-primary font-semibold text-sm py-3 rounded-full hover:bg-orange-50 transition-colors">
                    Get Started Free
                </a>
            </div>

            {{-- ---- PREMIUM (Most Popular) ---- --}}
            <div class="relative flex flex-col bg-white border-2 border-primary rounded-3xl p-8 shadow-lg shadow-orange-100">
                {{-- Badge --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                    <span class="bg-primary text-white text-xs font-bold px-5 py-1.5 rounded-full tracking-wide uppercase">
                        Most Popular
                    </span>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-bold text-heading mb-1">Premium</h3>
                    <p class="text-xs text-gray-400 mb-4">Unlimited swapping power</p>
                    <div class="flex items-end gap-1">
                        <span class="text-xs text-gray-400 mb-2">$</span>
                        <span class="text-5xl font-bold text-primary leading-none">19</span>
                        <span class="text-2xl font-bold text-primary mb-0.5">.99</span>
                        <span class="text-gray-400 text-sm mb-1">/mo</span>
                    </div>
                </div>

                <ul class="space-y-3 flex-1 mb-8">
                    @foreach ([
                        'Everything in Starter',
                        'Unlimited item value',
                        '10 photos per item',
                        'Unlimited trades',
                        'Priority matching',
                        'View real estate listings',
                    ] as $text)
                    <li class="flex items-center gap-3 text-sm">
                        <svg class="w-4 h-4 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-heading">{{ $text }}</span>
                    </li>
                    @endforeach
                </ul>

                <a href="/register"
                   class="block text-center bg-primary hover:bg-orange-600 text-white font-semibold text-sm py-3.5 rounded-full transition-colors shadow-sm">
                    Upgrade to Premium
                </a>
            </div>

            {{-- ---- PLATINUM ---- --}}
            <div class="relative flex flex-col bg-heading border border-gray-700 rounded-3xl p-8">
                {{-- Badge --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                    <span class="bg-yellow-600 text-white text-xs font-bold px-4 py-1.5 rounded-full tracking-wide uppercase">
                        Real Estate Pro
                    </span>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-bold text-white mb-1">Platinum</h3>
                    <p class="text-xs text-gray-400 mb-4">For serious property swappers</p>
                    <div class="flex items-end gap-1">
                        <span class="text-xs text-gray-400 mb-2">$</span>
                        <span class="text-5xl font-bold text-white leading-none">49</span>
                        <span class="text-2xl font-bold text-white mb-0.5">.99</span>
                        <span class="text-gray-400 text-sm mb-1">/mo</span>
                    </div>
                </div>

                <ul class="space-y-3 flex-1 mb-8">
                    @foreach ([
                        'Everything in Premium',
                        'List real estate properties',
                        'Contact property owners',
                        '20 photos + video upload',
                        'Virtual tour integration',
                        'Dedicated account manager',
                        'Priority support (4hr)',
                    ] as $text)
                    <li class="flex items-center gap-3 text-sm">
                        <svg class="w-4 h-4 text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-gray-200">{{ $text }}</span>
                    </li>
                    @endforeach
                </ul>

                <a href="/register"
                   class="block text-center bg-yellow-600 hover:bg-yellow-500 text-white font-semibold text-sm py-3.5 rounded-full transition-colors">
                    Upgrade to Platinum
                </a>
            </div>

        </div>
    </div>
</section>

@endsection

