<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book {{ $facilitySet->name }} (Consolidated Set) - EVSU Ormoc</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <style>
        .font-display { font-family: 'Franklin Gothic Medium', 'Franklin Gothic', 'Arial Black', sans-serif; }
        .font-body { font-family: 'Lucida Fax', 'Georgia', serif; }
        body { font-family: 'Lucida Fax', 'Georgia', serif; }
        .bg-warm-radial { background: radial-gradient(circle at top left, #f8f3ed 0%, #e8dbcb 45%, #c2a889 100%); }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-[#504538] font-body">

    <header class="bg-[#334c42] text-white py-4 shadow-md sticky top-0 z-50">
        <div class="mx-auto max-w-4xl px-4 flex justify-between items-center">
            <a href="{{ route('facilities.sets.show', $facilitySet) }}" class="flex items-center gap-2 hover:text-[#c2a889] transition-colors font-bold text-sm">
                <i class="fa-solid fa-arrow-left"></i> Back to Set Details
            </a>
            <h1 class="font-display tracking-wide uppercase text-sm md:text-base">Consolidated Booking Request</h1>
            <div class="w-16"></div>
        </div>
    </header>

    <main class="py-10">
        <div class="mx-auto max-w-4xl px-4">
            
            <div class="mb-6 text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#334c42]/10 text-[#334c42] text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-layer-group"></i> Consolidated Facility Set
                </div>
                <h2 class="text-3xl font-display text-[#334c42] mb-1">{{ $facilitySet->name }}</h2>
                <div class="text-[#627e71] font-bold">
                    @if($facilitySet->effective_hourly_rate && $facilitySet->effective_daily_rate)
                        ₱{{ number_format($facilitySet->effective_hourly_rate, 2) }}/hr &bull; ₱{{ number_format($facilitySet->effective_daily_rate, 2) }}/day
                    @else
                        ₱{{ number_format($facilitySet->rate, 2) }} / {{ ucfirst($facilitySet->rate_type) }}
                    @endif
                </div>
            </div>

            <!-- Consolidated Inclusion Notice -->
            <div class="bg-white rounded-2xl shadow-sm border border-[#d8c3ab] p-5 mb-8">
                <h3 class="text-sm font-bold text-[#334c42] uppercase tracking-wider mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    This reservation includes:
                </h3>
                <p class="text-xs text-[#627e71] mb-3">All member facilities will be reserved simultaneously for the specified schedule:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($facilitySet->facilities as $member)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#f8f3ed] border border-[#d8c3ab] text-xs font-bold text-[#334c42]">
                            <i class="fa-solid fa-check text-emerald-600 text-[10px]"></i>
                            {{ $member->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0"><i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i></div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-red-800">Booking conflict or validation error:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li class="font-medium">{{ $error }}</li>
                                @endforeach
                            </ul>
                            <p class="mt-2 text-xs text-red-600">Please adjust the date or time slot below to try another schedule.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid md:grid-cols-5 gap-8">
                
                <div class="md:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg border border-[#e8dbcb] overflow-hidden mb-6">
                        <div class="bg-[#e8dbcb] px-5 py-3 border-b border-[#d8c3ab]">
                            <h3 class="font-bold text-[#504538]"><i class="fa-solid fa-file-contract mr-2"></i>Terms &amp; Conditions</h3>
                        </div>
                        <div class="p-5 max-h-64 overflow-y-auto text-sm text-[#627e71] bg-[#f8f3ed]">
                            {!! nl2br(e($termsContent)) !!}
                        </div>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <form action="{{ route('facilities.sets.submit', $facilitySet) }}" method="POST" class="bg-white rounded-2xl shadow-lg border border-[#e8dbcb] p-6 md:p-8"
                          x-data="bookingCalculator({{ (float) ($facilitySet->effective_hourly_rate ?? $facilitySet->rate) }}, {{ (float) ($facilitySet->effective_daily_rate ?? $facilitySet->rate) }}, '{{ old('billing_type', request('billing_type', $facilitySet->rate_type ?? 'hourly')) }}')">
                        @csrf
                        
                        <h3 class="text-xl font-display text-[#334c42] border-b border-[#e8dbcb] pb-3 mb-6">Booker Information</h3>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-[#504538] mb-1">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="booker_name" value="{{ old('booker_name') }}" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="booker_email" value="{{ old('booker_email') }}" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" name="booker_contact" value="{{ old('booker_contact') }}" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                            </div>
                        </div>

                        <h3 class="text-xl font-display text-[#334c42] border-b border-[#e8dbcb] pb-3 mt-8 mb-6">Reservation Schedule</h3>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-[#504538] mb-1">Event Name</label>
                                <input type="text" name="event_name" value="{{ old('event_name', request('event_name')) }}" placeholder="e.g. Annual Department Conference" class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#504538] mb-1">Event Details / Purpose</label>
                                <textarea name="event_details" rows="3" placeholder="Describe the purpose, setup requirements, expected attendees, etc." class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">{{ old('event_details', request('event_details')) }}</textarea>
                            </div>

                            @if($facilitySet->effective_hourly_rate && $facilitySet->effective_daily_rate)
                            <div>
                                <label class="block text-sm font-bold text-[#504538] mb-1">Billing Option</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer" :class="billingType === 'hourly' ? 'border-[#334c42] bg-[#e8f0ec] text-[#334c42] font-bold' : 'border-[#d8c3ab] bg-[#f8f3ed] text-[#504538]'">
                                        <input type="radio" name="billing_type" value="hourly" x-model="billingType" class="text-[#334c42]">
                                        <span>Hourly (₱{{ number_format($facilitySet->effective_hourly_rate, 2) }}/hr)</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer" :class="billingType === 'daily' ? 'border-[#334c42] bg-[#e8f0ec] text-[#334c42] font-bold' : 'border-[#d8c3ab] bg-[#f8f3ed] text-[#504538]'">
                                        <input type="radio" name="billing_type" value="daily" x-model="billingType" class="text-[#334c42]">
                                        <span>Daily (₱{{ number_format($facilitySet->effective_daily_rate, 2) }}/day)</span>
                                    </label>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="billing_type" value="{{ $facilitySet->rate_type ?? 'hourly' }}" x-model="billingType">
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="reservation_date" min="{{ now()->format('Y-m-d') }}" value="{{ old('reservation_date', request('date', now()->format('Y-m-d'))) }}" x-model="startDate" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">End Date <span class="text-xs font-normal text-[#827567]">(Multi-day)</span></label>
                                    <input type="date" name="end_date" :min="startDate" value="{{ old('end_date', request('end_date')) }}" x-model="endDate" class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">Daily Start Time <span class="text-danger">*</span></label>
                                    <input type="time" name="start_time" value="{{ old('start_time', request('start_time', '08:00')) }}" x-model="startTime" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">Daily End Time <span class="text-danger">*</span></label>
                                    <input type="time" name="end_time" value="{{ old('end_time', request('end_time', '17:00')) }}" x-model="endTime" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 bg-[#334c42] text-white p-5 rounded-xl flex justify-between items-center shadow-inner">
                            <div>
                                <div class="font-bold">Estimated Total</div>
                                <div class="text-xs text-white/70" x-text="durationSummary"></div>
                            </div>
                            <div class="text-2xl font-display" x-text="'₱' + estimatedTotal.toFixed(2)">₱0.00</div>
                        </div>

                        <div class="mt-8 border-t border-[#e8dbcb] pt-6">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <div class="relative flex items-center pt-1">
                                    <input type="checkbox" name="terms_accepted" value="1" required class="w-5 h-5 accent-[#334c42] bg-[#f8f3ed] border-[#d8c3ab] rounded cursor-pointer">
                                </div>
                                <span class="text-sm text-[#504538] font-bold">
                                    I have read and agree to the Terms & Conditions and understand that this is a request subject to admin approval.
                                </span>
                            </label>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full bg-[#c2a889] hover:bg-[#b09677] text-[#504538] font-bold text-lg py-4 rounded-xl shadow-lg transition-transform active:scale-[0.98]">
                                Submit Consolidated Booking Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </main>

    <script>
        function bookingCalculator(hourlyRate, dailyRate, initialBillingType) {
            return {
                hourlyRate: hourlyRate,
                dailyRate: dailyRate,
                billingType: initialBillingType,
                startDate: '{{ old('reservation_date', request('date', now()->format('Y-m-d'))) }}',
                endDate: '{{ old('end_date', request('end_date', '')) }}',
                startTime: '{{ old('start_time', request('start_time', '08:00')) }}',
                endTime: '{{ old('end_time', request('end_time', '17:00')) }}',
                
                get durationHours() {
                    if (!this.startTime || !this.endTime) return 0;
                    const [sh, sm] = this.startTime.split(':').map(Number);
                    const [eh, em] = this.endTime.split(':').map(Number);
                    const diff = (eh + em / 60) - (sh + sm / 60);
                    return diff > 0 ? parseFloat(diff.toFixed(2)) : 0;
                },
                get totalDays() {
                    if (!this.startDate) return 1;
                    if (!this.endDate || this.endDate < this.startDate) return 1;
                    const s = new Date(this.startDate + 'T00:00:00');
                    const e = new Date(this.endDate + 'T00:00:00');
                    return Math.max(1, Math.round((e - s) / (1000 * 60 * 60 * 24)) + 1);
                },
                get estimatedTotal() {
                    const days = this.totalDays;
                    if (this.billingType === 'daily') {
                        return parseFloat((days * this.dailyRate).toFixed(2));
                    }
                    return parseFloat((this.durationHours * this.hourlyRate * days).toFixed(2));
                },
                get durationSummary() {
                    const days = this.totalDays;
                    if (this.billingType === 'daily') {
                        return days === 1 ? '1 Day (Flat rate)' : `${days} Days (Daily billing)`;
                    }
                    return days === 1 
                        ? `${this.durationHours} hrs × ₱${this.hourlyRate.toFixed(2)}` 
                        : `${days} Days × ${this.durationHours} hrs/day × ₱${this.hourlyRate.toFixed(2)}`;
                }
            };
        }
    </script>
</body>
</html>
