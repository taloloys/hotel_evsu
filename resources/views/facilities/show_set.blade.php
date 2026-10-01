<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $facilitySet->name }} - Consolidated Facility Set - EVSU HTM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

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

                <nav class="flex items-center text-xs sm:text-sm text-[#827567] font-semibold truncate"
                    aria-label="Breadcrumb">
                    <a href="{{ route('home') }}#facilities"
                        class="hover:text-[#334c42] transition-colors truncate">Facilities</a>
                    <i class="fa-solid fa-chevron-right mx-2 text-[10px] text-[#827567]/60 shrink-0"></i>
                    <span class="text-[#334c42] font-bold truncate">{{ $facilitySet->name }} (Consolidated Set)</span>
                </nav>
            </div>

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
        x-data="facilityBookingPlanner({{ (float) ($facilitySet->effective_hourly_rate ?? $facilitySet->rate) }}, {{ (float) ($facilitySet->effective_daily_rate ?? $facilitySet->rate) }}, '{{ $facilitySet->rate_type }}', {{ json_encode($reservationsData) }}, '{{ now()->format('Y-m-d') }}', '{{ route('facilities.sets.book', $facilitySet) }}', {{ ($facilitySet->hourly_rate !== null && $facilitySet->daily_rate !== null) ? 'true' : 'false' }})">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-3xl shadow-xl border border-[#827567]/20 overflow-hidden">

                <!-- Facility Set Title Banner -->
                <div class="bg-[#334c42] px-6 sm:px-8 py-6 text-white">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#c2a889] text-xs font-bold uppercase tracking-wider mb-2 border border-white/10">
                                <i class="fa-solid fa-layer-group"></i> Consolidated Facility Set
                            </div>
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-wide uppercase font-display text-white drop-shadow-sm">
                                {{ $facilitySet->name }}
                            </h1>
                        </div>
                        <div class="text-right">
                            <span class="text-xs uppercase tracking-wider text-white/70 block">Total Combined Capacity</span>
                            <span class="text-xl sm:text-2xl font-extrabold font-display text-[#c2a889]">
                                {{ $facilitySet->resolved_capacity }} Pax
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Included Facilities Callout (As specified in requirement) -->
                <div class="p-6 sm:p-8 bg-[#f8f3ed] border-b border-[#e8dbcb]">
                    <div class="max-w-4xl mx-auto bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-[#d8c3ab]">
                        <h2 class="text-base sm:text-lg font-bold text-[#334c42] mb-1 font-display flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            This reservation includes:
                        </h2>
                        <p class="text-xs text-[#627e71] mb-4">
                            Booking this facility set reserves all member facilities together for the exact same reservation period:
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($facilitySet->facilities as $member)
                                <div class="p-3.5 rounded-xl border border-[#d8c3ab] bg-[#f8f3ed]/60 flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm text-[#334c42]">{{ $member->name }}</div>
                                        <div class="text-xs text-[#827567] mt-0.5">
                                            @if($member->capacity)
                                                <span><i class="fa-solid fa-users me-1 text-[10px]"></i>{{ $member->capacity }} pax</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Details & Availability Section -->
                <div class="p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <!-- Left: Description and Rates -->
                    <div class="lg:col-span-5 space-y-6">
                        @if($facilitySet->description)
                            <div>
                                <h3 class="font-bold text-lg text-[#334c42] font-display mb-2">About This Facility Set</h3>
                                <p class="text-sm text-[#627e71] leading-relaxed whitespace-pre-line">{{ $facilitySet->description }}</p>
                            </div>
                        @endif

                        <div class="bg-[#f8f3ed] p-5 rounded-2xl border border-[#d8c3ab]">
                            <h4 class="font-bold text-sm text-[#827567] uppercase tracking-wider mb-3">Rental Rates</h4>
                            <div class="space-y-2">
                                @if($facilitySet->hourly_rate)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-[#504538] font-semibold">Hourly Rate:</span>
                                        <span class="font-bold text-[#334c42]">₱{{ number_format($facilitySet->hourly_rate, 2) }} / hr</span>
                                    </div>
                                @endif
                                @if($facilitySet->daily_rate)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-[#504538] font-semibold">Daily Rate:</span>
                                        <span class="font-bold text-[#334c42]">₱{{ number_format($facilitySet->daily_rate, 2) }} / day</span>
                                    </div>
                                @endif
                                @if(!$facilitySet->hourly_rate && !$facilitySet->daily_rate)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-[#504538] font-semibold">Standard Rate:</span>
                                        <span class="font-bold text-[#334c42]">₱{{ number_format($facilitySet->rate, 2) }} / {{ $facilitySet->rate_type }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Direct Proceed to Booking Button -->
                        <div class="pt-2">
                            <a :href="bookingUrl" class="w-full inline-flex items-center justify-center gap-2 bg-[#334c42] hover:bg-[#253930] text-white py-3.5 px-6 rounded-2xl font-bold shadow-md hover:shadow-lg transition-all active:scale-98">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>Proceed to Booking Form</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right: Schedule & Availability Planner -->
                    <div class="lg:col-span-7">
                        <div class="border border-[#d8c3ab] rounded-2xl p-5 sm:p-6 bg-white shadow-sm">
                            <h3 class="font-bold text-lg text-[#334c42] font-display mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-calendar-day text-[#c2a889]"></i>
                                Check Set Availability
                            </h3>
                            <p class="text-xs text-[#827567] mb-5">
                                Select a date to see if all member facilities are free. If any member facility is booked, the entire set cannot be reserved for that time.
                            </p>

                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-[#827567] uppercase tracking-wider mb-1">Reservation Date</label>
                                        <input type="date" x-model="startDate" min="{{ now()->format('Y-m-d') }}" @change="onStartDateChange" class="w-full rounded-xl border-[#d8c3ab] bg-[#f8f3ed] p-2.5 text-sm font-semibold text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-[#827567] uppercase tracking-wider mb-1">Rate Billing</label>
                                        <select x-model="rateType" class="w-full rounded-xl border-[#d8c3ab] bg-[#f8f3ed] p-2.5 text-sm font-semibold text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                            <option value="hourly">Hourly Billing</option>
                                            <option value="daily">Daily Billing</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4" x-show="rateType === 'hourly'">
                                    <div>
                                        <label class="block text-xs font-bold text-[#827567] uppercase tracking-wider mb-1">Start Time</label>
                                        <input type="time" x-model="startTime" class="w-full rounded-xl border-[#d8c3ab] bg-[#f8f3ed] p-2.5 text-sm font-semibold text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-[#827567] uppercase tracking-wider mb-1">End Time</label>
                                        <input type="time" x-model="endTime" class="w-full rounded-xl border-[#d8c3ab] bg-[#f8f3ed] p-2.5 text-sm font-semibold text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                    </div>
                                </div>

                                <!-- Existing approved reservations on this date -->
                                <div class="mt-4 pt-4 border-t border-[#e8dbcb]">
                                    <div class="text-xs font-bold text-[#827567] uppercase tracking-wider mb-2">Booked Slots for Selected Date</div>
                                    <template x-if="selectedPeriodReservations.length > 0">
                                        <div class="space-y-2">
                                            <template x-for="(res, idx) in selectedPeriodReservations" :key="idx">
                                                <div class="px-3 py-2 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 font-semibold flex items-center justify-between">
                                                    <span><i class="fa-solid fa-lock text-red-500 mr-1.5"></i> Occupied / Reserved</span>
                                                    <span x-text="`${res.start} - ${res.end}`"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="selectedPeriodReservations.length === 0">
                                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-semibold flex items-center gap-2">
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                            <span>No conflicting reservations found for this date. All member facilities are currently open!</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <footer class="bg-[#334c42] text-white py-6 text-center text-xs text-white/70">
        &copy; {{ date('Y') }} EVSU Ormoc Campus - Hotel, Resort &amp; Tourism Management Facilities
    </footer>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('facilityBookingPlanner', (hourlyRate, dailyRate, defaultRateType, reservations, todayDate, bookBaseUrl, hasBothRates) => ({
                hourlyRate: hourlyRate,
                dailyRate: dailyRate,
                rateType: defaultRateType || 'hourly',
                reservations: reservations || [],
                startDate: todayDate,
                endDate: todayDate,
                startTime: '08:00',
                endTime: '17:00',
                isRange: false,
                eventName: '',
                eventDetails: '',
                onStartDateChange() {
                    this.endDate = this.startDate;
                },
                get selectedPeriodReservations() {
                    if (!this.startDate) return [];
                    return this.reservations.filter(r => r.date === this.startDate);
                },
                get bookingUrl() {
                    let url = `${bookBaseUrl}?date=${this.startDate}&end_date=${this.endDate}&start_time=${this.startTime}&end_time=${this.endTime}&billing_type=${this.rateType}`;
                    if (this.eventName) {
                        url += `&event_name=${encodeURIComponent(this.eventName)}`;
                    }
                    if (this.eventDetails) {
                        url += `&event_details=${encodeURIComponent(this.eventDetails)}`;
                    }
                    return url;
                }
            }));
        });
    </script>
</body>

</html>
