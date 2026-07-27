<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hotel Don Felipe - Luxury Accommodations & Cafeteria Showcase</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icons/favicon-16x16.png') }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-serif-display {
            font-family: 'Playfair Display', Georgia, serif;
        }
        .glass-header {
            background: rgba(255, 253, 250, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .bg-warm-radial {
            background: radial-gradient(circle at top left, #fffbf7 0%, #f6efe7 45%, #ebe0d0 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-slate-800 antialiased selection:bg-[#a97142] selection:text-white">

    <!-- Sticky Navigation Header -->
    <header class="glass-header sticky top-0 z-50 border-b border-[#e8d9c7]/80 shadow-sm transition-all duration-300">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="#hero" class="group flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#e5d4bf] bg-white p-1.5 shadow-sm transition-transform duration-300 group-hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="Hotel Don Felipe Logo" class="h-full w-full object-contain" width="36" height="36">
                </div>
                <div>
                    <span class="block text-lg font-bold tracking-tight text-[#2f1c16] font-serif-display">Hotel Don Felipe</span>
                    <span class="block text-[10px] font-semibold tracking-wider text-[#a97142] uppercase">Hospitality & Dining</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-700">
                <a href="#hero" class="transition-colors hover:text-[#a97142]">Home</a>
                <a href="#rooms" class="transition-colors hover:text-[#a97142]">Rooms Showcase</a>
                <a href="#coffeeshop" class="transition-colors hover:text-[#a97142]">Cafeteria & Lounge</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#4e342e] to-[#a97142] px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-[#4e342e]/15 transition-all duration-200 hover:opacity-95 hover:shadow-lg active:scale-95">
                    <i class="fa-solid fa-lock text-xs"></i>
                    <span>Staff / Guest Login</span>
                </a>
                <button id="mobileMenuBtn" aria-label="Toggle mobile menu" class="md:hidden flex h-10 w-10 items-center justify-center rounded-xl border border-[#e8d9c7] bg-white text-slate-700">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Nav Menu -->
        <div id="mobileMenu" class="hidden border-t border-[#e8d9c7] bg-[#fcf9f5] px-4 py-4 md:hidden space-y-3">
            <a href="#hero" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-[#efe1cf]/50">Home</a>
            <a href="#rooms" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-[#efe1cf]/50">Rooms Showcase</a>
            <a href="#coffeeshop" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-[#efe1cf]/50">Cafeteria & Lounge</a>
            <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-bold text-[#a97142] hover:bg-[#efe1cf]/50">Staff Login Portal</a>
        </div>
    </header>

    <!-- Main Showcase Content -->
    <main>
        <!-- Hero Showcase Banner -->
        <section id="hero" class="relative overflow-hidden py-16 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto space-y-6">
                    
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#a97142]/30 bg-[#a97142]/10 px-4 py-1.5 text-xs font-bold text-[#6d4c41]">
                        <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                        <span>Welcome to Don Felipe Hotel & Cafeteria</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl font-extrabold text-[#2f1c16] tracking-tight font-serif-display leading-tight">
                        Experience Luxury, Tranquility & <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6d4c41] to-[#a97142]">Artisan Dining</span>
                    </h1>

                    <p class="text-base sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                        Immerse yourself in ultimate hospitality. Explore our full collection of accommodations and enjoy gourmet specialty coffee at our signature cafeteria.
                    </p>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                        <a href="#rooms" class="inline-flex items-center gap-2 rounded-2xl bg-[#4e342e] px-7 py-4 text-sm font-bold text-white shadow-xl shadow-[#4e342e]/20 transition-all hover:bg-[#6d4c41] hover:scale-[1.02]">
                            <i class="fa-solid fa-bed text-amber-200"></i>
                            <span>Explore Rooms Catalog</span>
                        </a>
                        <a href="#coffeeshop" class="inline-flex items-center gap-2 rounded-2xl border border-[#dccdb7] bg-white/90 px-7 py-4 text-sm font-bold text-[#4e342e] shadow-sm transition-all hover:bg-white hover:border-[#a97142]">
                            <i class="fa-solid fa-mug-hot text-[#a97142]"></i>
                            <span>Cafeteria Highlights</span>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-2xl bg-[#a97142] px-7 py-4 text-sm font-bold text-white shadow-md transition-all hover:bg-[#8c5a31]">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>Staff / Guest Login</span>
                        </a>
                    </div>

                    <!-- Highlights Bar -->
                    <div class="pt-12 grid grid-cols-2 md:grid-cols-4 gap-6 text-center border-t border-[#e8d9c7] mt-12">
                        <div class="p-4 rounded-2xl bg-white/60 border border-[#e8d9c7]/60">
                            <span class="block text-3xl font-extrabold text-[#2f1c16]">10+</span>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Room Options</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/60 border border-[#e8d9c7]/60">
                            <span class="block text-3xl font-extrabold text-[#2f1c16]">100%</span>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Arabica Coffee</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/60 border border-[#e8d9c7]/60">
                            <span class="block text-3xl font-extrabold text-[#2f1c16]">24/7</span>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Reception Service</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/60 border border-[#e8d9c7]/60">
                            <span class="block text-3xl font-extrabold text-[#2f1c16]">Prime</span>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">City Location</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Rooms Showcase Section (Multi-Image Gallery & No Descriptions) -->
        <section id="rooms" class="py-20 bg-white/70 border-t border-[#e8d9c7]/80">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#a97142]/20 bg-[#f8f3ec] px-4 py-1 text-xs font-bold text-[#a97142]">
                        <i class="fa-solid fa-hotel"></i>
                        <span>Luxury Accommodations</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-[#2f1c16] tracking-tight font-serif-display">
                        Our Signature Rooms Catalog
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600">
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
                            class="group relative flex flex-col overflow-hidden rounded-3xl border border-[#e8d9c7] bg-white shadow-lg shadow-[#4e342e]/5 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-[#a97142]/50">
                            
                            <!-- Room Image Gallery / Fallback Container -->
                            <div class="relative h-60 w-full overflow-hidden bg-gradient-to-br from-[#4e342e] via-[#6d4c41] to-[#a97142]">
                                @if (empty($imagesList))
                                    <!-- SVG Fallback Card (PHP Direct Render) -->
                                    <div class="flex flex-col items-center justify-center h-full w-full p-6 text-white text-center relative bg-gradient-to-br from-[#4e342e] to-[#a97142]">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 border border-white/30 backdrop-blur-sm mb-2">
                                            <i class="fa-solid {{ $room['icon'] ?? 'fa-bed' }} text-2xl text-amber-200"></i>
                                        </div>
                                        <span class="text-xs font-semibold text-amber-100 uppercase">{{ $room['category'] }}</span>
                                        <h4 class="text-base font-bold text-white mt-0.5">{{ $room['name'] }}</h4>
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
                                            <div class="hidden flex-col items-center justify-center h-full w-full p-6 text-white text-center relative bg-gradient-to-br from-[#4e342e] to-[#a97142]">
                                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 border border-white/30 backdrop-blur-sm mb-2">
                                                    <i class="fa-solid {{ $room['icon'] ?? 'fa-bed' }} text-2xl text-amber-200"></i>
                                                </div>
                                                <span class="text-xs font-semibold text-amber-100 uppercase">{{ $room['category'] }}</span>
                                                <h4 class="text-base font-bold text-white mt-0.5">{{ $room['name'] }}</h4>
                                            </div>
                                        </div>
                                    </template>
                                @endif

                                <!-- Badge Overlay -->
                                @if (!empty($room['badge']))
                                    <div class="absolute top-3 left-3 z-20 rounded-full bg-[#a97142] px-3 py-1 text-xs font-bold text-white shadow-md">
                                        {{ $room['badge'] }}
                                    </div>
                                @endif

                                <!-- Capacity Overlay -->
                                <div class="absolute top-3 right-3 z-20 rounded-full bg-black/60 backdrop-blur-sm px-3 py-1 text-xs font-semibold text-white">
                                    <i class="fa-solid fa-user-group mr-1 text-amber-300"></i>
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

                                <!-- Image Thumbnails Switcher Bar (If multiple images exist) -->
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

                            <!-- Card Body (Without Description as requested) -->
                            <div class="flex flex-1 flex-col justify-between p-6 space-y-4">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold uppercase tracking-wider text-[#a97142]">{{ $room['category'] }}</span>
                                        <span class="text-base font-extrabold text-[#2f1c16]">{{ $room['price'] }}</span>
                                    </div>

                                    <h3 class="text-xl font-bold text-[#2f1c16] leading-snug group-hover:text-[#a97142] transition-colors">
                                        {{ $room['name'] }}
                                    </h3>
                                </div>

                                <!-- Card Footer Actions & Amenities -->
                                <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                                    <div class="flex items-center gap-3 text-slate-400 text-xs">
                                        <span title="Free Wi-Fi"><i class="fa-solid fa-wifi"></i></span>
                                        <span title="Air Conditioned"><i class="fa-solid fa-snowflake"></i></span>
                                        <span title="Private Bathroom"><i class="fa-solid fa-shower"></i></span>
                                    </div>
                                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-[#a97142] px-3.5 py-2 rounded-xl transition-all hover:bg-[#8c5a31]">
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
        <section id="coffeeshop" class="py-20 bg-warm-radial border-t border-[#e8d9c7]/80">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
                
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#a97142]/20 bg-white px-4 py-1 text-xs font-bold text-[#a97142]">
                        <i class="fa-solid fa-mug-hot"></i>
                        <span>{{ $cafeteriaHero['category'] ?? 'Don Felipe Cafeteria & Lounge' }}</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-[#2f1c16] tracking-tight font-serif-display">
                        {{ $cafeteriaHero['title'] ?? 'Savor Handcrafted Coffee & Gourmet Culinary Treats' }}
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600">
                        Open daily for guests and visitors with artisan coffee, fresh bakery pastries, and all-day dining.
                    </p>
                </div>

                <!-- CAFETERIA HERO / MAIN IMAGE SHOWCASE CONTAINER -->
                <div class="overflow-hidden rounded-3xl border border-[#e8d9c7] bg-white shadow-xl grid lg:grid-cols-12 items-center">
                    <div class="lg:col-span-7 relative h-72 lg:h-96 w-full bg-gradient-to-br from-[#2f1c16] via-[#4e342e] to-[#6d4c41]">
                        @php
                            $mainCafeteriaImg = $cafeteriaHero['image'] ?? 'images/showcase/coffeeshop/cafeteria_main.jpg';
                            $hasMainImage = file_exists(public_path($mainCafeteriaImg));
                        @endphp
                        @if ($hasMainImage)
                            <img 
                                src="{{ asset($mainCafeteriaImg) }}" 
                                alt="Don Felipe Main Cafeteria"
                                loading="lazy"
                                width="700"
                                height="400"
                                class="h-full w-full object-cover">
                        @else
                            <!-- Main Cafeteria Placeholder Graphic -->
                            <div class="flex h-full w-full flex-col items-center justify-center p-8 text-white text-center relative">
                                <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-white/10 border border-white/20 backdrop-blur-md mb-4 shadow-inner">
                                    <i class="fa-solid fa-store text-4xl text-amber-300"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-white">Don Felipe Cafeteria & Lounge</h3>
                                <p class="text-xs text-amber-100 max-w-sm mt-1">Main Cafeteria Showcase Photograph Container</p>
                            </div>
                        @endif
                        <div class="absolute bottom-4 left-4 z-10 rounded-full bg-black/70 backdrop-blur-sm px-4 py-1.5 text-xs font-semibold text-white">
                            <i class="fa-solid fa-clock mr-1.5 text-amber-400"></i>
                            {{ $cafeteriaHero['timing'] }}
                        </div>
                    </div>

                    <div class="lg:col-span-5 p-8 sm:p-10 space-y-6">
                        <div class="space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#a97142]">Signature Ambiance</span>
                            <h3 class="text-2xl font-bold text-[#2f1c16] font-serif-display">Artisan Coffee & Cozy Lounge</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Enjoy comfortable seating, high-speed Wi-Fi, and a relaxed atmosphere whether you're meeting friends, relaxing after travel, or grabbing morning espresso.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-100 text-xs">
                            <div class="p-3 rounded-2xl bg-[#fffaf3] border border-[#f0dfc9]">
                                <span class="block font-bold text-[#2f1c16] text-sm">6:30 AM - 11:30 AM</span>
                                <span class="text-slate-500">Breakfast & Brews</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-[#fffaf3] border border-[#f0dfc9]">
                                <span class="block font-bold text-[#2f1c16] text-sm">11:30 AM - 10:00 PM</span>
                                <span class="text-slate-500">All-Day Dining</span>
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
                        <div class="group overflow-hidden rounded-3xl border border-[#e8d9c7] bg-white shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <div class="relative h-48 w-full bg-gradient-to-br from-[#2f1c16] via-[#4e342e] to-[#6d4c41] overflow-hidden">
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
                                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 border border-white/20 backdrop-blur-sm mb-2">
                                            <i class="fa-solid {{ $item['icon'] ?? 'fa-mug-hot' }} text-2xl text-amber-200"></i>
                                        </div>
                                        <span class="text-xs font-bold text-amber-100">{{ $item['category'] }}</span>
                                    </div>
                                @endif

                                <div class="absolute bottom-3 left-3 z-10 rounded-full bg-black/60 backdrop-blur-sm px-2.5 py-0.5 text-[11px] font-semibold text-white">
                                    {{ $item['timing'] }}
                                </div>
                            </div>

                            <div class="p-5 space-y-1.5">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-[#a97142]">{{ $item['category'] }}</span>
                                <h3 class="text-base font-bold text-[#2f1c16] group-hover:text-[#a97142] transition-colors">
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
    <footer class="border-t border-[#e8d9c7] bg-[#2f1c16] text-white/80 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-8 border-b border-white/10">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white p-1">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-full w-full object-contain">
                        </div>
                        <span class="text-lg font-bold text-white font-serif-display">Hotel Don Felipe</span>
                    </div>
                    <p class="text-xs text-white/60 leading-relaxed max-w-sm">
                        Providing luxury accommodations, fine dining, and unforgettable hospitality.
                    </p>
                </div>

                <div class="space-y-2 text-xs">
                    <h4 class="text-sm font-bold text-amber-200">Showcase Links</h4>
                    <ul class="space-y-1.5 text-white/70">
                        <li><a href="#hero" class="hover:text-white">Home Showcase</a></li>
                        <li><a href="#rooms" class="hover:text-white">Rooms Showcase</a></li>
                        <li><a href="#coffeeshop" class="hover:text-white">Cafeteria Highlights</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white">Staff / Guest Login</a></li>
                    </ul>
                </div>

                <div class="space-y-2 text-xs">
                    <h4 class="text-sm font-bold text-amber-200">Contact & Location</h4>
                    <p class="text-white/70"><i class="fa-solid fa-location-dot mr-2 text-amber-400"></i> Don Felipe Hotel, Main Street</p>
                    <p class="text-white/70"><i class="fa-solid fa-phone mr-2 text-amber-400"></i> Reception Desk: 24/7 Operations</p>
                    <p class="text-white/70"><i class="fa-solid fa-envelope mr-2 text-amber-400"></i> support@hoteldonfelipe.com</p>
                </div>
            </div>

            <div class="pt-6 text-center text-xs text-white/50">
                © {{ date('Y') }} Hotel Don Felipe. All rights reserved.
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
