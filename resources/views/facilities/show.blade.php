<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $facility->name }} - EVSU Ormoc</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <style>
        .font-display { font-family: 'Franklin Gothic Medium', 'Franklin Gothic', 'Arial Black', sans-serif; }
        .font-body { font-family: 'Lucida Fax', 'Georgia', serif; }
        body { font-family: 'Lucida Fax', 'Georgia', serif; }
        .glass-header { background: rgba(194, 168, 137, 0.95); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        .bg-warm-radial { background: radial-gradient(circle at top left, #f8f3ed 0%, #e8dbcb 45%, #c2a889 100%); }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-[#504538] font-body">

    <!-- Sticky Navigation Header (Simplified) -->
    <header class="glass-header sticky top-0 z-50 border-b border-[#827567]/30 shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}#facilities" class="group flex items-center gap-3">
                <div class="h-10 w-10 bg-[#334c42] rounded-full flex items-center justify-center text-white transition-transform group-hover:-translate-x-1">
                    <i class="fa-solid fa-arrow-left"></i>
                </div>
                <span class="font-bold text-[#504538]">Back to Facilities</span>
            </a>
        </div>
    </header>

    <main class="py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-[#827567]/20">
                
                <!-- Gallery Carousel -->
                @if(!empty($facility->images) && count($facility->images) > 0)
                <div x-data="{ activeSlide: 0, slides: {{ count($facility->images) }} }" class="relative h-64 md:h-96 bg-[#e8dbcb] group">
                    <div class="w-full h-full relative overflow-hidden">
                        @foreach($facility->images as $index => $img)
                            <div x-show="activeSlide === {{ $index }}"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-105"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute inset-0 w-full h-full">
                                <img src="{{ \App\Models\Facility::imageUrl($img) }}" alt="{{ $facility->name }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                    
                    @if(count($facility->images) > 1)
                    <!-- Prev/Next Arrows -->
                    <button @click="activeSlide = activeSlide === 0 ? slides - 1 : activeSlide - 1" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/70 text-[#334c42] flex items-center justify-center shadow-md hover:bg-white transition-colors">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button @click="activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/70 text-[#334c42] flex items-center justify-center shadow-md hover:bg-white transition-colors">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <!-- Indicators -->
                    <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2">
                        @foreach($facility->images as $index => $img)
                            <button @click="activeSlide = {{ $index }}" class="w-2.5 h-2.5 rounded-full transition-colors" :class="activeSlide === {{ $index }} ? 'bg-[#334c42]' : 'bg-white/50'"></button>
                        @endforeach
                    </div>
                    @endif
                </div>
                @else
                <div class="h-64 bg-[#e8dbcb] flex flex-col items-center justify-center text-[#827567]/60">
                    <i class="fa-solid fa-image text-5xl mb-2"></i>
                    <span>No images available</span>
                </div>
                @endif

                <div class="p-8 md:p-12">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-display text-[#334c42] mb-2">{{ $facility->name }}</h1>
                            @if($facility->capacity)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#e8f0ec] text-[#334c42] text-sm font-semibold">
                                    <i class="fa-solid fa-users"></i> Up to {{ $facility->capacity }} people
                                </span>
                            @endif
                        </div>
                        <div class="text-left md:text-right p-4 rounded-xl bg-[#f8f3ed] border border-[#e8dbcb]">
                            <div class="text-2xl font-bold text-[#334c42]">₱{{ number_format($facility->rate, 2) }}</div>
                            <div class="text-sm font-semibold text-[#627e71] uppercase tracking-wider">Per {{ ucfirst($facility->rate_type) }}</div>
                        </div>
                    </div>

                    <div class="prose max-w-none text-[#504538] mb-12">
                        {!! nl2br(e($facility->description)) !!}
                    </div>

                    <div class="grid md:grid-cols-2 gap-12">
                        <div>
                            <h3 class="text-xl font-display text-[#334c42] mb-4 border-b-2 border-[#e8dbcb] pb-2">Upcoming Reservations</h3>
                            @if($approvedReservations->isEmpty())
                                <p class="text-sm text-[#827567] italic">No confirmed reservations for the upcoming dates.</p>
                            @else
                                <ul class="space-y-3">
                                    @foreach($approvedReservations->take(10) as $res)
                                        <li class="flex items-center gap-3 text-sm p-3 rounded-lg bg-[#f8f3ed] border border-[#e8dbcb]">
                                            <div class="w-10 h-10 rounded-full bg-[#334c42]/10 flex items-center justify-center text-[#334c42]">
                                                <i class="fa-regular fa-calendar-check"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-[#504538]">{{ $res->reservation_date->format('M d, Y') }}</div>
                                                <div class="text-[#627e71]">{{ \Carbon\Carbon::parse($res->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('h:i A') }}</div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        
                        <div class="flex flex-col justify-center items-center p-8 bg-[#334c42] rounded-2xl text-center">
                            <h3 class="text-2xl font-display text-white mb-3">Ready to Book?</h3>
                            <p class="text-[#c2a889] text-sm mb-6">Submit a reservation request to secure this facility.</p>
                            <a href="{{ route('facilities.book', $facility) }}" class="inline-block w-full rounded-xl bg-[#c2a889] px-6 py-4 text-base font-bold text-[#504538] transition-all hover:bg-[#b09677] hover:scale-105 shadow-lg">
                                Book This Facility
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
