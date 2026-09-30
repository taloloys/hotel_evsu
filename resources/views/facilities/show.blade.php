<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $facility->name }} - EVSU HTM Facilities</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        .font-display {
            font-family: 'Franklin Gothic Medium', 'Franklin Gothic', 'Arial Black', sans-serif;
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

<body
    class="min-h-screen bg-warm-radial text-[#504538] font-body selection:bg-[#334c42] selection:text-white flex flex-col justify-between">

    <!-- Top Navigation Header -->
    <header class="glass-header sticky top-0 z-50 border-b border-[#827567]/30 shadow-sm transition-all duration-300">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 sm:gap-4 overflow-hidden">
                <a href="{{ route('home') }}#facilities"
                    class="group inline-flex items-center gap-2 rounded-full border border-[#827567]/30 bg-white/80 px-3.5 py-1.5 text-xs sm:text-sm font-bold text-[#504538] shadow-sm transition-all hover:bg-white hover:border-[#334c42] hover:text-[#334c42] active:scale-95 shrink-0">
                    <i class="fa-solid fa-arrow-left text-[11px] transition-transform group-hover:-translate-x-0.5"></i>
                    <span>Back</span>
                </a>

                <!-- Breadcrumbs -->
                <nav class="flex items-center text-xs sm:text-sm text-[#827567] font-semibold truncate"
                    aria-label="Breadcrumb">
                    <a href="{{ route('home') }}#facilities"
                        class="hover:text-[#334c42] transition-colors truncate">Facilities</a>
                    <i class="fa-solid fa-chevron-right mx-2 text-[10px] text-[#827567]/60 shrink-0"></i>
                    <span class="text-[#334c42] font-bold truncate">{{ $facility->name }}</span>
                </nav>
            </div>

            <!-- Department Badge -->
            <div class="hidden sm:flex items-center gap-2.5 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="HTM Logo" class="h-8 w-auto object-contain">
                <span class="text-xs font-bold text-[#334c42] font-display uppercase tracking-wider">HTM
                    Facilities</span>
            </div>
        </div>
    </header>

    @php
        $reservationsData = $approvedReservations->map(function ($r) {
            return [
                'date' => $r->reservation_date->format('Y-m-d'),
                'formatted_date' => $r->reservation_date->format('l, M d, Y'),
                'start' => \Carbon\Carbon::parse($r->start_time)->format('h:i A'),
                'end' => \Carbon\Carbon::parse($r->end_time)->format('h:i A'),
                'start_raw' => \Carbon\Carbon::parse($r->start_time)->format('H:i'),
                'end_raw' => \Carbon\Carbon::parse($r->end_time)->format('H:i'),
            ];
        })->values();
    @endphp

    <main class="flex-grow py-6 sm:py-8 lg:py-10 pb-28 md:pb-12"
        x-data="facilityBookingPlanner({{ (float) $facility->rate }}, '{{ $facility->rate_type }}', {{ json_encode($reservationsData) }}, '{{ now()->format('Y-m-d') }}', '{{ route('facilities.book', $facility) }}')">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            <!-- Facility Container -->
            <div class="bg-white rounded-3xl shadow-xl border border-[#827567]/20 overflow-hidden">

                <!-- Hero Gallery Section -->
                <div class="relative bg-[#e8dbcb]">
                    @if(!empty($facility->images) && count($facility->images) > 0)
                        <div x-data="{ activeSlide: 0, slides: {{ count($facility->images) }} }"
                            class="relative h-64 sm:h-80 md:h-[420px] w-full overflow-hidden group">
                            <!-- Slides -->
                            <div class="w-full h-full relative">
                                @foreach($facility->images as $index => $img)
                                    <div x-show="activeSlide === {{ $index }}"
                                        x-transition:enter="transition ease-out duration-400"
                                        x-transition:enter-start="opacity-0 scale-105"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-300"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95" class="absolute inset-0 w-full h-full">
                                        <img src="{{ \App\Models\Facility::imageUrl($img) }}" alt="{{ $facility->name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>

                            <!-- Carousel Controls (if multiple images) -->
                            @if(count($facility->images) > 1)
                                <button @click="activeSlide = activeSlide === 0 ? slides - 1 : activeSlide - 1"
                                    aria-label="Previous image"
                                    class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-white/80 backdrop-blur-sm text-[#334c42] flex items-center justify-center shadow-lg hover:bg-white hover:scale-105 active:scale-95 transition-all">
                                    <i class="fa-solid fa-chevron-left text-xs sm:text-sm"></i>
                                </button>
                                <button @click="activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1"
                                    aria-label="Next image"
                                    class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-white/80 backdrop-blur-sm text-[#334c42] flex items-center justify-center shadow-lg hover:bg-white hover:scale-105 active:scale-95 transition-all">
                                    <i class="fa-solid fa-chevron-right text-xs sm:text-sm"></i>
                                </button>

                                <!-- Dot Indicators -->
                                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-10">
                                    @foreach($facility->images as $index => $img)
                                        <button @click="activeSlide = {{ $index }}" aria-label="Slide {{ $index + 1 }}"
                                            class="h-2 rounded-full transition-all duration-300"
                                            :class="activeSlide === {{ $index }} ? 'w-6 bg-white' : 'w-2 bg-white/60 hover:bg-white/90'"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div
                            class="h-64 sm:h-80 md:h-[380px] bg-[#e8dbcb] flex flex-col items-center justify-center text-[#827567]/60">
                            <i class="fa-solid fa-image text-5xl mb-2"></i>
                            <span class="text-sm font-semibold">No images uploaded for this facility</span>
                        </div>
                    @endif

                    <!-- Facility Title Banner (Integrated Green Band) -->
                    <div class="bg-[#334c42] px-6 sm:px-8 py-5 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h1
                                    class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-wide uppercase font-display text-white drop-shadow-sm">
                                    {{ $facility->name }}
                                </h1>
                            </div>
                            <div class="shrink-0 pt-1 sm:pt-0">
                                <span
                                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-[#c2a889] text-xs font-bold tracking-wider uppercase shadow-sm">
                                    <i class="fa-solid fa-circle-check text-[#c2a889] text-xs"></i>
                                    <span>Available for Booking</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Rate & Key Information Card -->
                <div class="px-4 sm:px-8 -mt-4 relative z-20">
                    <div
                        class="bg-white rounded-2xl p-4 sm:p-6 shadow-xl border border-[#827567]/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <!-- Left info: Genuine Capacity badge -->
                        <div class="flex items-center gap-3">
                            <div
                                class="h-12 w-12 rounded-xl bg-[#e8f0ec] border border-[#334c42]/10 flex items-center justify-center text-[#334c42] shadow-sm shrink-0">
                                <i class="fa-solid fa-users text-lg"></i>
                            </div>
                            <div>
                                <div class="text-[11px] font-bold text-[#827567] uppercase tracking-wider">Capacity
                                </div>
                                <div class="text-base sm:text-lg font-extrabold text-[#504538]">
                                    @if($facility->capacity)
                                        Up to {{ $facility->capacity }} Persons
                                    @else
                                        Standard Capacity
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right info: Price and Rate Type -->
                        <div
                            class="text-left sm:text-right border-t sm:border-t-0 sm:border-l border-[#e8dbcb] pt-3 sm:pt-0 sm:pl-6">
                            <div class="flex items-baseline sm:justify-end gap-1.5">
                                <span
                                    class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#334c42] font-display">
                                    ₱{{ number_format($facility->rate, 2) }}
                                </span>
                                <span class="text-xs sm:text-sm font-bold text-[#627e71] uppercase tracking-wider">
                                    / {{ strtoupper($facility->rate_type === 'hourly' ? 'HR' : $facility->rate_type) }}
                                </span>
                            </div>
                            <div class="text-[11px] font-bold text-[#827567] uppercase tracking-wider mt-0.5">
                                Per {{ ucfirst($facility->rate_type) }} Rate
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Body -->
                <div class="p-4 sm:p-8 lg:p-10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                        <!-- Left Column: Description & Amenities & Reservations Schedule -->
                        <div class="lg:col-span-7 space-y-8">

                            <!-- Description & Amenities Card -->
                            <div class="rounded-2xl border border-[#e8dbcb] bg-[#f8f3ed]/60 p-6 sm:p-7 shadow-sm">
                                <h2
                                    class="text-xl sm:text-2xl font-bold font-display text-[#334c42] mb-4 flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-info text-[#627e71] text-lg"></i>
                                    <span>Description</span>
                                </h2>

                                <div class="prose max-w-none text-[#504538] text-sm sm:text-base leading-relaxed mb-6">
                                    {!! nl2br(e($facility->description)) !!}
                                </div>

                                <!-- Standard Features & Inclusions -->

                            </div>

                            <!-- Upcoming Reservations Section -->
                            <div class="rounded-2xl border border-[#e8dbcb] bg-white p-6 sm:p-7 shadow-sm">
                                <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#e8dbcb]">
                                    <div>
                                        <h3 class="text-lg sm:text-xl font-bold font-display text-[#334c42]">
                                            Upcoming Reservations
                                        </h3>
                                        <p class="text-xs text-[#827567] mt-0.5">
                                            Confirmed booking schedule for this venue
                                        </p>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 rounded-full bg-[#f8f3ed] border border-[#e8dbcb] text-[#627e71] text-[11px] font-bold">
                                        {{ $approvedReservations->count() }} Confirmed
                                    </span>
                                </div>

                                @if($approvedReservations->isEmpty())
                                    <div
                                        class="text-center py-8 px-4 rounded-xl bg-[#f8f3ed]/60 border border-dashed border-[#e8dbcb]">
                                        <div
                                            class="h-12 w-12 rounded-full bg-[#e8f0ec] text-[#334c42] flex items-center justify-center mx-auto mb-3">
                                            <i class="fa-regular fa-calendar-check text-xl"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-[#504538] mb-1">No Upcoming Reservations</h4>
                                        <p class="text-xs text-[#827567] max-w-sm mx-auto">
                                            All upcoming dates and time slots are currently open. Choose your preferred date
                                            on the calendar planner to reserve!
                                        </p>
                                    </div>
                                @else
                                    <!-- List of upcoming bookings -->
                                    <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
                                        @foreach($approvedReservations->take(10) as $res)
                                            <div
                                                class="flex items-center justify-between p-3.5 rounded-xl bg-[#f8f3ed] border border-[#e8dbcb]/70 hover:border-[#627e71]/40 transition-colors">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-xl bg-[#334c42]/10 text-[#334c42] flex items-center justify-center font-bold text-xs shrink-0">
                                                        <i class="fa-regular fa-calendar-days text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-sm text-[#504538]">
                                                            {{ $res->reservation_date->format('l, M d, Y') }}
                                                        </div>
                                                        <div
                                                            class="text-xs font-semibold text-[#627e71] flex items-center gap-1.5 mt-0.5">
                                                            <i class="fa-regular fa-clock text-[11px]"></i>
                                                            <span>{{ \Carbon\Carbon::parse($res->start_time)->format('h:i A') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($res->end_time)->format('h:i A') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#e8dbcb]/70 text-[#504538] text-[11px] font-bold shrink-0">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-[#334c42]"></span>
                                                    Reserved
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>

                        <!-- Right Column: Interactive Booking Calendar & Sticky Launcher -->
                        <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">

                            <!-- Ready to Book Card with Interactive Calendar -->
                            <div
                                class="rounded-3xl bg-[#334c42] text-white p-6 sm:p-7 shadow-xl relative overflow-hidden">
                                <!-- Subtle decorative circle -->
                                <div
                                    class="absolute -right-12 -top-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none">
                                </div>

                                <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-3">
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#c2a889] text-xs font-bold uppercase tracking-wider">
                                            <i class="fa-regular fa-calendar-plus"></i> Select Date &amp; Time
                                        </div>
                                        <span class="text-xs font-bold text-[#c2a889]"
                                            x-text="rateType === 'hourly' ? 'Hourly Rental' : 'Daily Rental'"></span>
                                    </div>

                                    <h3 class="text-2xl font-extrabold font-display text-white mb-1">
                                        Ready to Book?
                                    </h3>
                                    <p class="text-xs text-[#c2a889] mb-5 leading-relaxed">
                                        Pick your date and time below. Your choices will automatically pre-fill on the
                                        booking page.
                                    </p>

                                    <!-- Interactive Calendar Card -->
                                    <div
                                        class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 mb-5">

                                        <!-- Month Selector Header -->
                                        <div
                                            class="flex items-center justify-between mb-3 pb-2 border-b border-white/10">
                                            <button type="button" @click="prevMonth()" aria-label="Previous Month"
                                                class="h-7 w-7 rounded-lg bg-white/10 hover:bg-white/20 text-[#c2a889] hover:text-white flex items-center justify-center transition-colors">
                                                <i class="fa-solid fa-chevron-left text-xs"></i>
                                            </button>
                                            <div class="font-bold text-sm tracking-wide text-white uppercase font-display"
                                                x-text="monthNames[currentMonth] + ' ' + currentYear">
                                            </div>
                                            <button type="button" @click="nextMonth()" aria-label="Next Month"
                                                class="h-7 w-7 rounded-lg bg-white/10 hover:bg-white/20 text-[#c2a889] hover:text-white flex items-center justify-center transition-colors">
                                                <i class="fa-solid fa-chevron-right text-xs"></i>
                                            </button>
                                        </div>

                                        <!-- Days of Week Header -->
                                        <div
                                            class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-[#c2a889] uppercase tracking-wider mb-1.5">
                                            <span>Su</span>
                                            <span>Mo</span>
                                            <span>Tu</span>
                                            <span>We</span>
                                            <span>Th</span>
                                            <span>Fr</span>
                                            <span>Sa</span>
                                        </div>

                                        <!-- Calendar Days Grid -->
                                        <div class="grid grid-cols-7 gap-1 text-center text-xs">
                                            <!-- Empty slots for previous month offset -->
                                            <template x-for="blank in firstDayOfWeek" :key="'blank-' + blank">
                                                <div class="h-8 w-8"></div>
                                            </template>

                                            <!-- Days in current month -->
                                            <template x-for="day in daysInMonth" :key="'day-' + day">
                                                <button type="button" @click="selectDay(day)" :disabled="isPast(day)"
                                                    class="h-8 w-8 mx-auto flex flex-col items-center justify-center rounded-xl transition-all duration-150 relative"
                                                    :class="{
                                                            'cursor-not-allowed opacity-25 text-white/40': isPast(day),
                                                            'bg-[#c2a889] text-[#334c42] font-black shadow-lg scale-105 ring-2 ring-white': isSelected(day),
                                                            'hover:bg-white/20 text-white': !isSelected(day) && !isPast(day)
                                                        }">
                                                    <span x-text="day" class="text-xs"></span>
                                                    <!-- Dot for dates that have approved reservations -->
                                                    <template x-if="hasReservation(day)">
                                                        <span class="w-1.5 h-1.5 rounded-full absolute bottom-0.5"
                                                            :class="isSelected(day) ? 'bg-[#334c42]' : 'bg-amber-400'"></span>
                                                    </template>
                                                </button>
                                            </template>
                                        </div>

                                    </div>

                                    <!-- Selected Date Confirmation & Reservation Status -->
                                    <div class="bg-white/10 rounded-xl p-3 border border-white/15 mb-4">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-[#c2a889] font-bold">Selected Date:</span>
                                            <span class="font-extrabold text-white"
                                                x-text="formattedSelectedDate"></span>
                                        </div>

                                        <!-- If the selected date has existing confirmed bookings -->
                                        <template x-if="selectedDateReservations.length > 0">
                                            <div
                                                class="mt-2 pt-2 border-t border-white/10 text-[11px] text-amber-200 flex items-start gap-1.5">
                                                <i
                                                    class="fa-solid fa-triangle-exclamation text-amber-300 mt-0.5 shrink-0"></i>
                                                <span>
                                                    <strong x-text="selectedDateReservations.length"></strong> existing
                                                    reservation on this day
                                                    (<span
                                                        x-text="selectedDateReservations.map(r => r.start + ' - ' + r.end).join(', ')"></span>).
                                                    Please pick a free window!
                                                </span>
                                            </div>
                                        </template>

                                        <!-- If the selected date is completely free -->
                                        <template x-if="selectedDateReservations.length === 0">
                                            <div
                                                class="mt-2 pt-2 border-t border-white/10 text-[11px] text-emerald-300 flex items-center gap-1.5">
                                                <i class="fa-solid fa-circle-check shrink-0"></i>
                                                <span>Date has 100% availability. All slots are open!</span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Time Selection (for hourly rates) -->
                                    <template x-if="rateType === 'hourly'">
                                        <div class="space-y-3 mb-5">
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label
                                                        class="block text-[11px] font-bold text-[#c2a889] mb-1 uppercase tracking-wider">Start
                                                        Time</label>
                                                    <input type="time" x-model="startTime"
                                                        class="w-full rounded-xl bg-white/10 border border-white/20 px-3 py-2 text-xs font-bold text-white focus:outline-none focus:ring-2 focus:ring-[#c2a889]">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[11px] font-bold text-[#c2a889] mb-1 uppercase tracking-wider">End
                                                        Time</label>
                                                    <input type="time" x-model="endTime"
                                                        class="w-full rounded-xl bg-white/10 border border-white/20 px-3 py-2 text-xs font-bold text-white focus:outline-none focus:ring-2 focus:ring-[#c2a889]">
                                                </div>
                                            </div>

                                            <!-- Quick duration presets -->
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-[10px] text-[#c2a889] font-bold uppercase tracking-wider">Quick:</span>
                                                <button type="button" @click="setDuration(2)"
                                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">2
                                                    hrs</button>
                                                <button type="button" @click="setDuration(4)"
                                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">4
                                                    hrs</button>
                                                <button type="button" @click="setDuration(8)"
                                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">8
                                                    hrs</button>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Estimated Price Preview Box -->
                                    <div
                                        class="bg-black/20 rounded-2xl p-4 mb-5 border border-white/10 flex items-center justify-between">
                                        <div>
                                            <div class="text-[10px] font-bold text-[#c2a889] uppercase tracking-wider">
                                                Estimated Total</div>
                                            <div class="text-xs text-white/70" x-show="rateType === 'hourly'">
                                                <span x-text="durationHours"></span> hours ×
                                                ₱{{ number_format($facility->rate, 2) }}
                                            </div>
                                            <div class="text-xs text-white/70" x-show="rateType === 'daily'">
                                                Full Day Booking
                                            </div>
                                        </div>
                                        <div class="text-2xl font-extrabold text-[#c2a889] font-display">
                                            ₱<span x-text="estimatedTotal.toFixed(2)"></span>
                                        </div>
                                    </div>

                                    <!-- Primary Call to Action Button -->
                                    <a :href="bookingUrl"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#c2a889] px-6 py-4 text-base font-extrabold text-[#504538] shadow-lg transition-all duration-200 hover:bg-[#d4bd9f] hover:scale-[1.02] active:scale-95">
                                        <span>Book This Facility</span>
                                        <i class="fa-solid fa-arrow-right text-sm"></i>
                                    </a>

                                    <p class="text-[11px] text-[#c2a889]/80 text-center mt-3 font-semibold">
                                        *Selected date &amp; hours will auto-fill on the booking &amp; T&amp;C page
                                    </p>
                                </div>
                            </div>



                        </div>

                    </div>
                </div>

            </div>

        </div>

        <!-- Mobile Fixed Bottom Action Bar -->
        <div
            class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md px-4 py-3 border-t border-[#827567]/20 shadow-2xl flex items-center justify-between gap-3">
            <div>
                <div class="text-[10px] font-bold text-[#827567] uppercase tracking-wider">Estimated Total</div>
                <div class="text-lg font-extrabold text-[#334c42] font-display">
                    ₱<span x-text="estimatedTotal.toFixed(2)"></span>
                </div>
            </div>
            <a :href="bookingUrl"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#334c42] px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-[#273a33] active:scale-95 transition-all">
                <span>Book Facility</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </main>

    <!-- Refined Footer -->
    <footer class="border-t border-[#827567]/30 bg-[#504538] text-white/90 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-8 border-b border-[#827567]/40">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto object-contain">
                        <span class="text-lg font-bold text-white font-display">HTM Department</span>
                    </div>
                    <p class="text-xs text-[#c2a889] leading-relaxed max-w-sm">
                        Hospitality & Tourism Management Department — Providing luxury accommodations, fine dining, and
                        hands-on hospitality excellence.
                    </p>
                </div>

                <div class="space-y-2 text-xs">
                    <h4 class="text-sm font-bold text-[#c2a889] font-display uppercase tracking-wider">Quick Links</h4>
                    <ul class="space-y-2 text-white/80">
                        <li><a href="{{ route('home') }}"
                                class="hover:text-[#c2a889] transition-colors flex items-center gap-2"><i
                                    class="fa-solid fa-angle-right text-[10px] text-[#c2a889]"></i> Home Showcase</a>
                        </li>
                        <li><a href="{{ route('home') }}#rooms"
                                class="hover:text-[#c2a889] transition-colors flex items-center gap-2"><i
                                    class="fa-solid fa-angle-right text-[10px] text-[#c2a889]"></i> Rooms Showcase</a>
                        </li>
                        <li><a href="{{ route('home') }}#facilities"
                                class="hover:text-[#c2a889] transition-colors flex items-center gap-2"><i
                                    class="fa-solid fa-angle-right text-[10px] text-[#c2a889]"></i> Facilities List</a>
                        </li>
                        <li><a href="{{ route('facilities.terms') }}"
                                class="hover:text-[#c2a889] transition-colors flex items-center gap-2"><i
                                    class="fa-solid fa-angle-right text-[10px] text-[#c2a889]"></i> Facility Terms &amp;
                                Conditions</a></li>
                    </ul>
                </div>

                <div class="space-y-2 text-xs">
                    <h4 class="text-sm font-bold text-[#627e71] font-display">Contact & Location</h4>
                    <p class="text-[#c2a889]"><i class="fa-solid fa-location-dot mr-2 text-[#627e71]"></i> EVSU HTM
                        Department, Ormoc City</p>
                    <p class="text-[#c2a889]"><i class="fa-solid fa-phone mr-2 text-[#627e71]"></i> Reception Desk: 24/7
                        Operations</p>
                    <p class="text-[#c2a889]"><i class="fa-solid fa-envelope mr-2 text-[#627e71]"></i> htm@evsu.edu.ph
                    </p>
                </div>
            </div>

            <div class="pt-6 text-center text-xs text-[#c2a889]/70">
                © {{ date('Y') }} EVSU Hospitality & Tourism Management Department. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('facilityBookingPlanner', (rate, rateType, reservations, initialDate, bookBaseUrl) => {
                const today = new Date();
                const todayStr = today.toISOString().split('T')[0];

                let startD = initialDate;
                if (!startD || startD < todayStr) {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    startD = tomorrow.toISOString().split('T')[0];
                }

                const [initY, initM] = startD.split('-').map(Number);

                return {
                    selectedDate: startD,
                    startTime: '08:00',
                    endTime: '12:00',
                    currentYear: initY,
                    currentMonth: initM - 1, // 0-indexed for JS Date
                    rate: parseFloat(rate),
                    rateType: rateType,
                    reservations: reservations || [],
                    monthNames: [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ],

                    get todayString() {
                        const now = new Date();
                        const y = now.getFullYear();
                        const m = String(now.getMonth() + 1).padStart(2, '0');
                        const d = String(now.getDate()).padStart(2, '0');
                        return `${y}-${m}-${d}`;
                    },
                    get daysInMonth() {
                        return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    },
                    get firstDayOfWeek() {
                        return new Date(this.currentYear, this.currentMonth, 1).getDay();
                    },
                    prevMonth() {
                        if (this.currentMonth === 0) {
                            this.currentMonth = 11;
                            this.currentYear--;
                        } else {
                            this.currentMonth--;
                        }
                    },
                    nextMonth() {
                        if (this.currentMonth === 11) {
                            this.currentMonth = 0;
                            this.currentYear++;
                        } else {
                            this.currentMonth++;
                        }
                    },
                    formatDayString(day) {
                        const m = String(this.currentMonth + 1).padStart(2, '0');
                        const d = String(day).padStart(2, '0');
                        return `${this.currentYear}-${m}-${d}`;
                    },
                    isPast(day) {
                        return this.formatDayString(day) < this.todayString;
                    },
                    isSelected(day) {
                        return this.selectedDate === this.formatDayString(day);
                    },
                    selectDay(day) {
                        if (this.isPast(day)) return;
                        this.selectedDate = this.formatDayString(day);
                    },
                    hasReservation(day) {
                        const dateStr = this.formatDayString(day);
                        return this.reservations.some(r => r.date === dateStr);
                    },
                    get formattedSelectedDate() {
                        if (!this.selectedDate) return '';
                        const parts = this.selectedDate.split('-').map(Number);
                        const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                        return dateObj.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
                    },
                    get selectedDateReservations() {
                        return this.reservations.filter(r => r.date === this.selectedDate);
                    },
                    setDuration(hours) {
                        if (!this.startTime) this.startTime = '08:00';
                        const [sh, sm] = this.startTime.split(':').map(Number);
                        let endH = sh + hours;
                        if (endH >= 24) endH = 23;
                        this.endTime = `${String(endH).padStart(2, '0')}:${String(sm).padStart(2, '0')}`;
                    },
                    get durationHours() {
                        if (!this.startTime || !this.endTime) return 0;
                        const [sh, sm] = this.startTime.split(':').map(Number);
                        const [eh, em] = this.endTime.split(':').map(Number);
                        const diff = (eh + em / 60) - (sh + sm / 60);
                        return diff > 0 ? parseFloat(diff.toFixed(2)) : 0;
                    },
                    get estimatedTotal() {
                        if (this.rateType === 'daily') {
                            return this.rate;
                        }
                        return parseFloat((this.durationHours * this.rate).toFixed(2));
                    },
                    get bookingUrl() {
                        return `${bookBaseUrl}?date=${this.selectedDate}&start_time=${this.startTime}&end_time=${this.endTime}`;
                    }
                };
            });
        });
    </script>
</body>

</html>