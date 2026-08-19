<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HTM Department - EVSU Accommodations & Cafeteria Showcase</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icons/favicon-16x16.png') }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&family=Caveat:wght@600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        @font-face {
            font-family: 'Quick Kiss';
            src: local('Quick Kiss');
        }

        .font-display {
            font-family: 'Franklin Gothic Medium', 'Franklin Gothic', 'Arial Black', sans-serif;
        }
        .font-script {
            font-family: 'Quick Kiss', 'Great Vibes', 'Caveat', cursive;
        }
        .font-body {
            font-family: 'Lucida Fax', 'Georgia', serif;
        }

        body {
            font-family: 'Lucida Fax', 'Georgia', serif;
        }
        .glass-header {
            background: rgba(194, 168, 137, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .bg-warm-radial {
            background: radial-gradient(circle at top left, #f8f3ed 0%, #e8dbcb 45%, #c2a889 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-[#504538] antialiased selection:bg-[#334c42] selection:text-white font-body">

    <!-- Sticky Navigation Header -->
    <header class="glass-header sticky top-0 z-50 border-b border-[#827567]/30 shadow-sm transition-all duration-300">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="#hero" class="group flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="HTM Department Logo" class="h-12 w-auto object-contain transition-transform duration-300 group-hover:scale-105" width="48" height="48">
                <div>
                    <span class="block text-base font-bold tracking-tight text-[#504538] font-display">Hospitality & Tourism</span>
                    <span class="block text-[10px] font-semibold tracking-wider text-[#334c42] uppercase font-body">Management Department</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-[#504538]">
                <a href="#hero" class="transition-colors hover:text-[#334c42]">Home</a>
                <a href="#rooms" class="transition-colors hover:text-[#334c42]">Rooms Showcase</a>
                <a href="#coffeeshop" class="transition-colors hover:text-[#334c42]">Cafeteria & Lounge</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#334c42] px-5 py-2.5 text-xs font-bold text-white shadow-md transition-all duration-200 hover:bg-[#627e71] active:scale-95">
                    <i class="fa-solid fa-lock text-xs"></i>
                    <span>Staff / Guest Login</span>
                </a>
                <button id="mobileMenuBtn" aria-label="Toggle mobile menu" class="md:hidden flex h-10 w-10 items-center justify-center rounded-xl border border-[#827567]/40 bg-white text-[#504538]">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Nav Menu -->
        <div id="mobileMenu" class="hidden border-t border-[#827567]/30 bg-[#c2a889] px-4 py-4 md:hidden space-y-3">
            <a href="#hero" class="block rounded-lg px-3 py-2 text-sm font-semibold text-[#504538] hover:bg-[#827567]/20">Home</a>
            <a href="#rooms" class="block rounded-lg px-3 py-2 text-sm font-semibold text-[#504538] hover:bg-[#827567]/20">Rooms Showcase</a>
            <a href="#coffeeshop" class="block rounded-lg px-3 py-2 text-sm font-semibold text-[#504538] hover:bg-[#827567]/20">Cafeteria & Lounge</a>
            <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-bold text-[#334c42] hover:bg-[#827567]/20">Staff Login Portal</a>
        </div>
    </header>

    <!-- Main Showcase Content -->
    <main>
        <!-- Hero Showcase Banner -->
        <section id="hero" class="relative overflow-hidden py-16 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto space-y-6">
                    
                    <div class="inline-flex items-center gap-2 rounded-full bg-[#827567] px-4 py-1.5 text-xs font-bold text-white shadow-sm">
                        <i class="fa-solid fa-star text-[#c2a889] text-xs"></i>
                        <span class="font-display">Welcome to EVSU Hotel & Cafeteria</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl font-extrabold text-[#504538] tracking-tight font-display leading-tight">
                        Experience Luxury, Tranquility & <span class="font-script text-[#627e71] text-5xl sm:text-7xl font-normal">Artisan Dining</span>
                    </h1>

                    <p class="text-base sm:text-xl text-[#827567] max-w-2xl mx-auto leading-relaxed font-body">
                        Immerse yourself in ultimate hospitality. Explore our full collection of accommodations and enjoy gourmet specialty coffee at our signature cafeteria.
                    </p>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                        <a href="#rooms" class="inline-flex items-center gap-2 rounded-2xl bg-[#334c42] px-7 py-4 text-sm font-bold text-white shadow-xl shadow-[#334c42]/20 transition-all hover:bg-[#627e71] hover:scale-[1.02]">
                            <i class="fa-solid fa-bed text-[#c2a889]"></i>
                            <span>Explore Rooms Catalog</span>
                        </a>
                        <a href="#coffeeshop" class="inline-flex items-center gap-2 rounded-2xl border-2 border-[#c2a889] bg-white/90 px-7 py-4 text-sm font-bold text-[#504538] shadow-sm transition-all hover:bg-[#c2a889] hover:text-[#504538]">
                            <i class="fa-solid fa-mug-hot text-[#334c42]"></i>
                            <span>Cafeteria Highlights</span>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-2xl bg-[#627e71] px-7 py-4 text-sm font-bold text-white shadow-md transition-all hover:bg-[#334c42]">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>Staff / Guest Login</span>
                        </a>
                    </div>

                    <!-- Highlights Bar -->
                    <div class="pt-12 grid grid-cols-2 md:grid-cols-4 gap-6 text-center border-t border-[#827567]/30 mt-12">
                        <div class="p-4 rounded-2xl bg-[#c2a889]/30 border border-[#827567]/30 backdrop-blur-sm">
                            <span class="block text-3xl font-extrabold text-[#334c42] font-display">Premium</span>
                            <span class="text-xs font-semibold text-[#504538] uppercase tracking-wider font-display">Accommodations</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[#c2a889]/30 border border-[#827567]/30 backdrop-blur-sm">
                            <span class="block text-3xl font-extrabold text-[#334c42] font-display">Signature</span>
                            <span class="text-xs font-semibold text-[#504538] uppercase tracking-wider font-display">Coffee Blends</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[#c2a889]/30 border border-[#827567]/30 backdrop-blur-sm">
                            <span class="block text-3xl font-extrabold text-[#334c42] font-display">24/7</span>
                            <span class="text-xs font-semibold text-[#504538] uppercase tracking-wider font-display">Concierge</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[#c2a889]/30 border border-[#827567]/30 backdrop-blur-sm">
                            <span class="block text-3xl font-extrabold text-[#334c42] font-display">EVSU</span>
                            <span class="text-xs font-semibold text-[#504538] uppercase tracking-wider font-display">Main Campus</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Rooms Showcase Section -->
        <section id="rooms" class="py-20 bg-white/70 border-t border-[#827567]/30">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#827567]/30 bg-[#c2a889]/30 px-4 py-1 text-xs font-bold text-[#334c42]">
                        <i class="fa-solid fa-hotel"></i>
                        <span>Luxury Accommodations</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-[#504538] tracking-tight font-display">
                        Our Signature Rooms Catalog
                    </h2>
                    <p class="text-sm sm:text-base text-[#827567]">
                        Explore room options featuring modern amenities, comfortable bedding, and elegant decor.
                    </p>
                </div>

                <!-- Rooms Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($rooms as $room)
                        @php
                            $imagesList = is_array($room['images']) ? $room['images'] : [$room['images'] ?? 'images/showcase/rooms/standard.jpg'];
                        @endphp
                        
                        <div 
                            x-data="{ activeIndex: 0, images: {{ json_encode($imagesList) }} }"
                            class="group relative flex flex-col overflow-hidden rounded-3xl border border-[#827567]/30 bg-white shadow-lg shadow-[#504538]/5 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-[#334c42]/50">
                            
                            <!-- Room Image Gallery / Fallback Container -->
                            <div class="relative h-60 w-full overflow-hidden bg-gradient-to-br from-[#504538] via-[#334c42] to-[#627e71]">
                                @if (empty($imagesList))
                                    <!-- SVG Fallback Card (PHP Direct Render) -->
                                    <div class="flex flex-col items-center justify-center h-full w-full p-6 text-white text-center relative bg-gradient-to-br from-[#504538] via-[#334c42] to-[#627e71]">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 border border-white/30 backdrop-blur-sm mb-2">
                                            <i class="fa-solid {{ $room['icon'] ?? 'fa-bed' }} text-2xl text-[#c2a889]"></i>
                                        </div>
                                        <span class="text-xs font-semibold text-[#c2a889] uppercase font-display">{{ $room['category'] }}</span>
                                        <h4 class="text-base font-bold text-white mt-0.5 font-display">{{ $room['name'] }}</h4>
                                    </div>
                                @else
                                    <template x-for="(imgSrc, idx) in images" :key="idx">
                                        <div x-show="activeIndex === idx" class="h-full w-full">
                                            <img 
                                                :src="'{{ asset('') }}' + imgSrc" 
                                                alt="{{ $room['name'] }}"
                                                loading="lazy"
                                                width="400"
                                                height="260"
                                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                            
                                            <!-- SVG Fallback Card (JS Fallback) -->
                                            <div class="hidden flex-col items-center justify-center h-full w-full p-6 text-white text-center relative bg-gradient-to-br from-[#504538] via-[#334c42] to-[#627e71]">
                                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 border border-white/30 backdrop-blur-sm mb-2">
                                                    <i class="fa-solid {{ $room['icon'] ?? 'fa-bed' }} text-2xl text-[#c2a889]"></i>
                                                </div>
                                                <span class="text-xs font-semibold text-[#c2a889] uppercase font-display">{{ $room['category'] }}</span>
                                                <h4 class="text-base font-bold text-white mt-0.5 font-display">{{ $room['name'] }}</h4>
                                            </div>
                                        </div>
                                    </template>
                                @endif

                                <!-- Badge Overlay -->
                                @if (!empty($room['badge']))
                                    <div class="absolute top-3 left-3 z-20 rounded-full bg-[#334c42] px-3 py-1 text-xs font-bold text-white shadow-md font-display">
                                        {{ $room['badge'] }}
                                    </div>
                                @endif

                                <!-- Capacity Overlay -->
                                <div class="absolute top-3 right-3 z-20 rounded-full bg-black/60 backdrop-blur-sm px-3 py-1 text-xs font-semibold text-white">
                                    <i class="fa-solid fa-user-group mr-1 text-[#c2a889]"></i>
                                    {{ $room['capacity'] }}
                                </div>

                                <!-- Left/Right Navigation Arrows -->
                                <button 
                                    type="button"
                                    x-show="images.length > 1" 
                                    @click="activeIndex = activeIndex === 0 ? images.length - 1 : activeIndex - 1"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 z-20 flex h-7 w-7 items-center justify-center rounded-full bg-black/40 hover:bg-black/60 text-white backdrop-blur-sm transition-all focus:outline-none">
                                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                                </button>
                                <button 
                                    type="button"
                                    x-show="images.length > 1" 
                                    @click="activeIndex = activeIndex === images.length - 1 ? 0 : activeIndex + 1"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 z-20 flex h-7 w-7 items-center justify-center rounded-full bg-black/40 hover:bg-black/60 text-white backdrop-blur-sm transition-all focus:outline-none">
                                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </button>

                                <!-- Image Thumbnails Switcher Bar -->
                                <div x-show="images.length > 1" class="absolute bottom-3 inset-x-0 z-20 flex justify-center gap-1.5 px-3">
                                    <template x-for="(imgSrc, idx) in images" :key="idx">
                                        <button 
                                            @click="activeIndex = idx"
                                            :class="activeIndex === idx ? 'bg-white w-6' : 'bg-white/50 w-2.5 hover:bg-white/80'"
                                            aria-label="View room image"
                                            class="h-2 rounded-full transition-all duration-300 shadow-sm"></button>
                                    </template>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="flex flex-1 flex-col justify-between p-6 space-y-4">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold uppercase tracking-wider text-[#627e71] font-display">{{ $room['category'] }}</span>
                                        <span class="text-base font-extrabold text-[#504538] font-display">{{ $room['price'] }}</span>
                                    </div>

                                    <h3 class="text-xl font-bold text-[#504538] leading-snug group-hover:text-[#627e71] transition-colors font-display">
                                        {{ $room['name'] }}
                                    </h3>
                                </div>

                                <!-- Card Footer Actions & Amenities -->
                                <div class="flex items-center justify-between border-t border-[#827567]/20 pt-4">
                                    <div class="flex items-center gap-3 text-[#827567] text-xs">
                                        <span title="Free Wi-Fi"><i class="fa-solid fa-wifi"></i></span>
                                        <span title="Air Conditioned"><i class="fa-solid fa-snowflake"></i></span>
                                        <span title="Private Bathroom"><i class="fa-solid fa-shower"></i></span>
                                    </div>
                                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-[#627e71] px-3.5 py-2 rounded-xl transition-all hover:bg-[#334c42]">
                                        <span>Inquire / Book</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Cafeteria & Lounge Showcase Section -->
        <section id="coffeeshop" class="py-20 bg-warm-radial border-t border-[#827567]/30">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
                
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#827567]/30 bg-[#c2a889]/30 px-4 py-1 text-xs font-bold text-[#334c42]">
                        <i class="fa-solid fa-mug-hot"></i>
                        <span>{{ $cafeteriaHero['category'] ?? 'EVSU Cafeteria & Lounge' }}</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-[#504538] tracking-tight font-display">
                        {{ $cafeteriaHero['title'] ?? 'Savor Handcrafted Coffee & Gourmet Culinary Treats' }}
                    </h2>
                    <p class="text-sm sm:text-base text-[#827567]">
                        Open daily for guests and visitors with artisan coffee, fresh bakery pastries, and all-day dining.
                    </p>
                </div>

                <!-- CAFETERIA HERO / MAIN IMAGE SHOWCASE CONTAINER -->
                <div class="overflow-hidden rounded-3xl border border-[#827567]/30 bg-white shadow-xl grid lg:grid-cols-12 items-center">
                    <div class="lg:col-span-7 relative h-72 lg:h-96 w-full bg-gradient-to-br from-[#504538] via-[#334c42] to-[#627e71]">
                        @php
                            $mainCafeteriaImg = $cafeteriaHero['image'] ?? 'images/showcase/coffeeshop/cafeteria_main.jpg';
                            $hasMainImage = file_exists(public_path($mainCafeteriaImg));
                        @endphp
                        @if ($hasMainImage)
                            <img 
                                src="{{ asset($mainCafeteriaImg) }}" 
                                alt="EVSU Main Cafeteria"
                                loading="lazy"
                                width="700"
                                height="400"
                                class="h-full w-full object-cover">
                        @else
                            <!-- Main Cafeteria Placeholder Graphic -->
                            <div class="flex h-full w-full flex-col items-center justify-center p-8 text-white text-center relative">
                                <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-white/10 border border-white/20 backdrop-blur-md mb-4 shadow-inner">
                                    <i class="fa-solid fa-store text-4xl text-[#c2a889]"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-white font-display">EVSU Cafeteria & Lounge</h3>
                                <p class="text-xs text-[#c2a889] max-w-sm mt-1">Main Cafeteria Showcase Photograph Container</p>
                            </div>
                        @endif
                        <div class="absolute bottom-4 left-4 z-10 rounded-full bg-black/70 backdrop-blur-sm px-4 py-1.5 text-xs font-semibold text-white">
                            <i class="fa-solid fa-clock mr-1.5 text-[#c2a889]"></i>
                            {{ $cafeteriaHero['timing'] }}
                        </div>
                    </div>

                    <div class="lg:col-span-5 p-8 sm:p-10 space-y-6">
                        <div class="space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#627e71] font-display">Signature Ambiance</span>
                            <h3 class="text-2xl font-bold text-[#504538] font-display">Artisan Coffee & Cozy Lounge</h3>
                            <p class="text-sm text-[#827567] leading-relaxed">
                                Enjoy comfortable seating, high-speed Wi-Fi, and a relaxed atmosphere whether you're meeting friends, relaxing after travel, or grabbing morning espresso.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-[#827567]/20 text-xs">
                            <div class="p-3 rounded-2xl bg-[#c2a889]/30 border border-[#827567]/30">
                                <span class="block font-bold text-[#504538] text-sm font-display">6:30 AM - 11:30 AM</span>
                                <span class="text-[#827567]">Breakfast & Brews</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-[#c2a889]/30 border border-[#827567]/30">
                                <span class="block font-bold text-[#504538] text-sm font-display">11:30 AM - 10:00 PM</span>
                                <span class="text-[#827567]">All-Day Dining</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coffeeshop Items Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($coffeeshopHighlights as $item)
                        @php
                            $hasItemImage = file_exists(public_path($item['image']));
                        @endphp
                        <div class="group overflow-hidden rounded-3xl border border-[#827567]/30 bg-white shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <div class="relative h-48 w-full bg-gradient-to-br from-[#504538] to-[#334c42] overflow-hidden">
                                @if ($hasItemImage)
                                    <img 
                                        src="{{ asset($item['image']) }}" 
                                        alt="{{ $item['title'] }}"
                                        loading="lazy"
                                        width="320"
                                        height="200"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full flex-col items-center justify-center p-6 text-white text-center relative">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#627e71] border border-white/20 backdrop-blur-sm mb-2">
                                            <i class="fa-solid {{ $item['icon'] ?? 'fa-mug-hot' }} text-2xl text-[#c2a889]"></i>
                                        </div>
                                        <span class="text-xs font-bold text-[#c2a889] font-display">{{ $item['category'] }}</span>
                                    </div>
                                @endif

                                <div class="absolute bottom-3 left-3 z-10 rounded-full bg-[#827567] px-2.5 py-0.5 text-[11px] font-semibold text-white">
                                    {{ $item['timing'] }}
                                </div>
                            </div>

                            <div class="p-5 space-y-1.5">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-[#627e71] font-display">{{ $item['category'] }}</span>
                                <h3 class="text-base font-bold text-[#504538] group-hover:text-[#627e71] transition-colors font-display">
                                    {{ $item['title'] }}
                                </h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#827567]/30 bg-[#504538] text-white/90 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-8 border-b border-[#827567]/40">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto object-contain">
                        <span class="text-lg font-bold text-white font-display">HTM Department</span>
                    </div>
                    <p class="text-xs text-[#c2a889] leading-relaxed max-w-sm">
                        Hospitality & Tourism Management Department — Providing luxury accommodations, fine dining, and hands-on hospitality excellence.
                    </p>
                </div>

                <div class="space-y-2 text-xs">
                    <h4 class="text-sm font-bold text-[#627e71] font-display">Showcase Links</h4>
                    <ul class="space-y-1.5 text-[#c2a889]">
                        <li><a href="#hero" class="hover:text-white transition-colors">Home Showcase</a></li>
                        <li><a href="#rooms" class="hover:text-white transition-colors">Rooms Showcase</a></li>
                        <li><a href="#coffeeshop" class="hover:text-white transition-colors">Cafeteria Highlights</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Staff / Guest Login</a></li>
                    </ul>
                </div>

                <div class="space-y-2 text-xs">
                    <h4 class="text-sm font-bold text-[#627e71] font-display">Contact & Location</h4>
                    <p class="text-[#c2a889]"><i class="fa-solid fa-location-dot mr-2 text-[#627e71]"></i> EVSU HTM Department, Tacloban City</p>
                    <p class="text-[#c2a889]"><i class="fa-solid fa-phone mr-2 text-[#627e71]"></i> Reception Desk: 24/7 Operations</p>
                    <p class="text-[#c2a889]"><i class="fa-solid fa-envelope mr-2 text-[#627e71]"></i> htm@evsu.edu.ph</p>
                </div>
            </div>

            <div class="pt-6 text-center text-xs text-[#c2a889]/70">
                © {{ date('Y') }} EVSU Hospitality & Tourism Management Department. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('mobileMenuBtn');
        const menu = document.getElementById('mobileMenu');
        if (btn && menu) {
            btn.addEventListener('click', function () {
                menu.classList.toggle('hidden');
            });
        }
    });
    </script>
</body>
</html>
