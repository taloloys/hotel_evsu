<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book {{ $facility->name }} - EVSU Ormoc</title>
    <meta name="description" content="Book {{ $facility->name }} at EVSU Lodging & Conference Center. Review our Terms & Conditions and submit your booking request.">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @include('partials.pwa')

    <style>
        .font-display { font-family: 'Franklin Gothic Medium', 'Franklin Gothic', 'Arial Black', sans-serif; }
        .font-body { font-family: 'Lucida Fax', 'Georgia', serif; }
        body { font-family: 'Lucida Fax', 'Georgia', serif; }
        .bg-warm-radial { background: radial-gradient(ellipse at top, #f8f3ed 0%, #e8dbcb 50%, #c2a889 100%); }

        /* Step indicator */
        .step-dot {
            width: 2.5rem; height: 2.5rem;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.875rem;
            transition: all 0.4s ease;
        }
        .step-dot.active { background: #334c42; color: #fff; box-shadow: 0 0 0 4px #334c42/20; }
        .step-dot.done { background: #627e71; color: #fff; }
        .step-dot.pending { background: #e8dbcb; color: #827567; border: 2px solid #d8c3ab; }

        /* Terms scroll area */
        .terms-scroll {
            max-height: 55vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #c2a889 #f8f3ed;
        }
        .terms-scroll::-webkit-scrollbar { width: 6px; }
        .terms-scroll::-webkit-scrollbar-track { background: #f8f3ed; border-radius: 3px; }
        .terms-scroll::-webkit-scrollbar-thumb { background: #c2a889; border-radius: 3px; }

        /* Slide transition */
        [x-cloak] { display: none !important; }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .slide-in-right { animation: slideInRight 0.45s cubic-bezier(.22,.61,.36,1) both; }
        .slide-in-left  { animation: slideInLeft  0.45s cubic-bezier(.22,.61,.36,1) both; }

        /* Form inputs */
        .form-input {
            width: 100%; border-radius: 0.5rem;
            border: 1.5px solid #d8c3ab;
            background: #f8f3ed;
            padding: 0.75rem;
            color: #504538;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #334c42;
            box-shadow: 0 0 0 3px rgba(51,76,66,.12);
        }
        .form-label { display: block; font-size: .875rem; font-weight: 700; color: #504538; margin-bottom: .25rem; }

        /* Pulse for scroll indicator */
        @keyframes bounceY {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(5px); }
        }
        .bounce-y { animation: bounceY 1.5s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-[#504538] font-body"
      x-data="bookingFlow({{ (float) ($facility->effective_hourly_rate ?? $facility->rate) }}, {{ (float) ($facility->effective_daily_rate ?? $facility->rate) }}, '{{ old('billing_type', request('billing_type', $facility->rate_type ?? 'hourly')) }}')">

    {{-- ====== HEADER ====== --}}
    <header class="bg-[#334c42] text-white py-4 shadow-md sticky top-0 z-50">
        <div class="mx-auto max-w-3xl px-4 flex items-center justify-between">
            <a href="{{ route('facilities.show', $facility) }}" class="flex items-center gap-2 hover:text-[#c2a889] transition-colors font-bold text-sm">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
            <h1 class="font-display tracking-wide uppercase text-sm md:text-base">Booking Request</h1>
            <div class="w-16"></div>
        </div>
    </header>

    <main class="py-10 px-4">
        <div class="mx-auto max-w-3xl">

            {{-- Facility title --}}
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-display text-[#334c42] mb-1">{{ $facility->name }}</h2>
                <div class="text-[#627e71] font-bold">₱{{ number_format($facility->rate, 2) }} / {{ ucfirst($facility->rate_type) }}</div>
            </div>

            {{-- ====== STEP INDICATOR ====== --}}
            <div class="flex items-center justify-center gap-4 mb-10">
                {{-- Step 1 --}}
                <div class="flex flex-col items-center gap-1">
                    <div class="step-dot" :class="step === 1 ? 'active' : 'done'">
                        <span x-show="step === 1">1</span>
                        <i x-show="step === 2" class="fa-solid fa-check text-sm"></i>
                    </div>
                    <span class="text-xs font-bold" :class="step === 1 ? 'text-[#334c42]' : 'text-[#627e71]'">Terms & Conditions</span>
                </div>
                {{-- Connector --}}
                <div class="flex-1 max-w-[80px] h-0.5 rounded" :class="step === 2 ? 'bg-[#334c42]' : 'bg-[#d8c3ab]'"></div>
                {{-- Step 2 --}}
                <div class="flex flex-col items-center gap-1">
                    <div class="step-dot" :class="step === 2 ? 'active' : 'pending'">2</div>
                    <span class="text-xs font-bold" :class="step === 2 ? 'text-[#334c42]' : 'text-[#827567]'">Booking Form</span>
                </div>
            </div>

            {{-- ============================== --}}
            {{-- STEP 1: TERMS & CONDITIONS     --}}
            {{-- ============================== --}}
            <div x-show="step === 1" x-cloak class="slide-in-left">
                <div class="bg-white rounded-2xl shadow-xl border border-[#e8dbcb] overflow-hidden">

                    {{-- T&C Header --}}
                    <div class="bg-[#334c42] px-7 py-5 border-b-4 border-[#c2a889] flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#c2a889]/20 flex items-center justify-center">
                            <i class="fa-solid fa-file-contract text-[#c2a889] text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-display text-white text-lg tracking-wide">Venue Rental Terms & Conditions</h3>
                            <p class="text-[#c2a889] text-xs mt-0.5">Please read all the terms carefully before proceeding</p>
                        </div>
                    </div>

                    {{-- T&C Body --}}
                    <div class="p-6 md:p-8">
                        <div class="terms-scroll text-sm text-[#504538] leading-relaxed bg-[#f8f3ed] rounded-xl p-5 border border-[#e8dbcb]"
                             id="terms-container"
                             x-ref="termsContainer"
                             @scroll.passive="checkScrolled">
                            {!! nl2br(e($termsContent)) !!}
                        </div>

                        {{-- Scroll nudge / status --}}
                        <div class="mt-3 flex items-center justify-between px-1">
                            <div x-show="!scrolledToBottom" class="flex items-center gap-1.5 text-xs text-[#827567]">
                                <span class="inline-flex items-center gap-1.5 bounce-y font-medium">
                                    <i class="fa-solid fa-angles-down text-[#334c42]"></i> Scroll down inside the terms box to unlock the agreement
                                </span>
                            </div>
                            <div x-show="scrolledToBottom" x-cloak class="flex items-center gap-1.5 text-xs text-emerald-700 font-semibold">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Terms read completely — you may now agree and continue
                            </div>

                            <button type="button"
                                    x-show="!scrolledToBottom"
                                    @click="scrollToBottom()"
                                    class="text-xs font-bold text-[#334c42] hover:text-[#253930] hover:underline flex items-center gap-1 ml-auto">
                                Scroll to bottom <i class="fa-solid fa-arrow-down text-[10px]"></i>
                            </button>
                        </div>

                        {{-- Agreement checkbox --}}
                        <label class="flex items-start gap-3 mt-5 p-4 rounded-xl border transition-all duration-200"
                               :class="scrolledToBottom
                                   ? (agreed ? 'bg-emerald-50/70 border-emerald-300 cursor-pointer shadow-sm' : 'bg-[#fcfaf7] border-[#d8c3ab] hover:border-[#334c42] cursor-pointer')
                                   : 'bg-[#f4eee6]/60 border-dashed border-[#d8c3ab] cursor-not-allowed opacity-75'">
                            <div class="relative flex items-center pt-0.5 flex-shrink-0">
                                <input type="checkbox" id="agree_terms" x-model="agreed"
                                       :disabled="!scrolledToBottom"
                                       class="w-5 h-5 rounded border-[#d8c3ab] transition-colors"
                                       :class="scrolledToBottom ? 'accent-[#334c42] cursor-pointer' : 'cursor-not-allowed opacity-50'">
                            </div>
                            <div class="flex-1 text-sm leading-relaxed"
                                 :class="scrolledToBottom ? 'text-[#504538]' : 'text-[#827567]'">
                                <span>
                                    I have <strong>read and fully understood</strong> the Terms &amp; Conditions above, and I agree to abide by them. I also understand that this is a <strong>booking request</strong> subject to review and approval by the Front Desk / Admin.
                                </span>
                                <div x-show="!scrolledToBottom" class="text-xs text-[#a06a3b] mt-1.5 flex items-center gap-1.5 font-semibold">
                                    <i class="fa-solid fa-lock text-xs"></i> Checkbox is disabled until the terms above are read to the bottom.
                                </div>
                            </div>
                        </label>

                        {{-- Proceed button --}}
                        <div class="mt-6">
                            <button type="button"
                                    id="btn-proceed-to-form"
                                    @click="proceedToForm()"
                                    :disabled="!agreed"
                                    :class="agreed
                                        ? 'bg-[#334c42] hover:bg-[#253930] text-white shadow-lg cursor-pointer active:scale-[0.98]'
                                        : 'bg-[#e8dbcb] text-[#b09677] cursor-not-allowed'"
                                    class="w-full font-bold text-base py-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-check"></i>
                                I Agree — Proceed to Booking Form
                            </button>
                        </div>

                        <p class="text-center text-xs text-[#827567] mt-3">
                            <i class="fa-solid fa-lock mr-1"></i>
                            Your information will be kept confidential and used only for reservation purposes.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ============================== --}}
            {{-- STEP 2: BOOKING FORM          --}}
            {{-- ============================== --}}
            <div x-show="step === 2" x-cloak class="slide-in-right">

                {{-- Agreed badge --}}
                <div class="flex items-center gap-2 mb-5 bg-green-50 border border-green-200 text-green-800 text-sm font-bold px-4 py-2.5 rounded-xl shadow-sm">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                    Terms & Conditions accepted — please complete your booking details below.
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-xl shadow-sm">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
                            <div>
                                <h3 class="text-sm font-bold text-red-800">Please correct the following errors:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('facilities.submit', $facility) }}" method="POST"
                      class="bg-white rounded-2xl shadow-xl border border-[#e8dbcb] p-6 md:p-8">
                    @csrf
                    <input type="hidden" name="terms_accepted" value="1">

                    {{-- ── Booker Information ── --}}
                    <div class="flex items-center gap-3 border-b border-[#e8dbcb] pb-3 mb-6">
                        <div class="w-8 h-8 rounded-full bg-[#334c42]/10 flex items-center justify-center">
                            <i class="fa-solid fa-user text-[#334c42] text-sm"></i>
                        </div>
                        <h3 class="text-xl font-display text-[#334c42]">Booker Information</h3>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label for="booker_name" class="form-label">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" id="booker_name" name="booker_name"
                                   value="{{ old('booker_name') }}" required
                                   placeholder="e.g. Juan Dela Cruz"
                                   class="form-input">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="booker_email" class="form-label">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" id="booker_email" name="booker_email"
                                       value="{{ old('booker_email') }}" required
                                       placeholder="you@example.com"
                                       class="form-input">
                            </div>
                            <div>
                                <label for="booker_contact" class="form-label">Contact Number <span class="text-red-500">*</span></label>
                                <input type="text" id="booker_contact" name="booker_contact"
                                       value="{{ old('booker_contact') }}" required
                                       placeholder="09xx-xxx-xxxx"
                                       class="form-input">
                            </div>
                        </div>
                    </div>

                    {{-- ── Reservation Schedule ── --}}
                    <div class="flex items-center gap-3 border-b border-[#e8dbcb] pb-3 mt-8 mb-6">
                        <div class="w-8 h-8 rounded-full bg-[#334c42]/10 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-days text-[#334c42] text-sm"></i>
                        </div>
                        <h3 class="text-xl font-display text-[#334c42]">Reservation Schedule</h3>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label for="event_name" class="form-label">Event Name</label>
                            <input type="text" id="event_name" name="event_name"
                                   value="{{ old('event_name', request('event_name')) }}"
                                   placeholder="e.g. Annual Department Conference"
                                   class="form-input">
                        </div>

                        <div>
                            <label for="event_details" class="form-label">Event Details / Purpose</label>
                            <textarea id="event_details" name="event_details" rows="3"
                                      placeholder="Describe the purpose, setup requirements, expected attendees, etc."
                                      class="form-input resize-none">{{ old('event_details', request('event_details')) }}</textarea>
                        </div>

                        @if($facility->hourly_rate && $facility->daily_rate)
                        <div>
                            <label class="form-label">Billing Option</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                       :class="billingType === 'hourly' ? 'border-[#334c42] bg-[#e8f0ec] text-[#334c42] font-bold' : 'border-[#d8c3ab] bg-[#f8f3ed] text-[#504538]'">
                                    <input type="radio" name="billing_type" value="hourly" x-model="billingType">
                                    <span>Hourly (₱{{ number_format($facility->hourly_rate, 2) }}/hr)</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                       :class="billingType === 'daily' ? 'border-[#334c42] bg-[#e8f0ec] text-[#334c42] font-bold' : 'border-[#d8c3ab] bg-[#f8f3ed] text-[#504538]'">
                                    <input type="radio" name="billing_type" value="daily" x-model="billingType">
                                    <span>Daily (₱{{ number_format($facility->daily_rate, 2) }}/day)</span>
                                </label>
                            </div>
                        </div>
                        @else
                        <input type="hidden" name="billing_type" value="{{ $facility->rate_type ?? 'hourly' }}" x-model="billingType">
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="reservation_date" class="form-label">Start Date <span class="text-red-500">*</span></label>
                                <input type="date" id="reservation_date" name="reservation_date"
                                       min="{{ now()->format('Y-m-d') }}"
                                       value="{{ old('reservation_date', request('date', now()->format('Y-m-d'))) }}"
                                       x-model="startDate" required class="form-input">
                            </div>
                            <div>
                                <label for="end_date" class="form-label">
                                    End Date <span class="text-xs font-normal text-[#827567]">(Multi-day, optional)</span>
                                </label>
                                <input type="date" id="end_date" name="end_date"
                                       :min="startDate"
                                       value="{{ old('end_date', request('end_date')) }}"
                                       x-model="endDate" class="form-input">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="start_time" class="form-label">Daily Start Time <span class="text-red-500">*</span></label>
                                <input type="time" id="start_time" name="start_time"
                                       value="{{ old('start_time', request('start_time', '08:00')) }}"
                                       x-model="startTime" required class="form-input">
                            </div>
                            <div>
                                <label for="end_time" class="form-label">Daily End Time <span class="text-red-500">*</span></label>
                                <input type="time" id="end_time" name="end_time"
                                       value="{{ old('end_time', request('end_time', '12:00')) }}"
                                       x-model="endTime" required class="form-input">
                            </div>
                        </div>
                    </div>

                    {{-- ── Estimated Total ── --}}
                    <div class="mt-8 bg-[#334c42] text-white p-5 rounded-2xl flex justify-between items-center shadow-inner">
                        <div>
                            <div class="font-bold text-base">Estimated Total</div>
                            <div class="text-xs text-white/70 mt-0.5" x-text="durationSummary"></div>
                        </div>
                        <div class="text-3xl font-display" x-text="'₱' + estimatedTotal.toFixed(2)">₱0.00</div>
                    </div>

                    {{-- ── Pending notice ── --}}
                    <div class="mt-5 bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
                        <i class="fa-solid fa-hourglass-half text-yellow-600 mt-0.5 flex-shrink-0"></i>
                        <div class="text-sm text-yellow-800">
                            <strong>Important:</strong> Your booking will be marked as <strong>PENDING</strong> after submission. It is not confirmed until the Front Desk / Admin reviews and approves your request.
                        </div>
                    </div>

                    {{-- ── Submit ── --}}
                    <div class="mt-6 grid grid-cols-1 gap-3">
                        <button type="submit" id="btn-submit-booking"
                                class="w-full bg-[#c2a889] hover:bg-[#b09677] text-[#504538] font-bold text-lg py-4 rounded-xl shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            Submit Booking Request
                        </button>
                        <button type="button" @click="step = 1"
                                class="w-full text-sm text-[#827567] hover:text-[#504538] py-2 transition-colors underline underline-offset-2">
                            ← Back to Terms & Conditions
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingFlow', (hourlyRate, dailyRate, initialBillingType) => ({
                step: {{ $errors->any() ? 2 : 1 }},
                agreed: {{ old('terms_accepted') || $errors->any() ? 'true' : 'false' }},
                scrolledToBottom: {{ old('terms_accepted') || $errors->any() ? 'true' : 'false' }},

                // Calculator state
                hourlyRate: parseFloat(hourlyRate || 0),
                dailyRate: parseFloat(dailyRate || 0),
                billingType: initialBillingType || 'hourly',
                startDate: '{{ old('reservation_date', request('date', now()->format('Y-m-d'))) }}',
                endDate: '{{ old('end_date', request('end_date', '')) }}',
                startTime: '{{ old('start_time', request('start_time', '08:00')) }}',
                endTime: '{{ old('end_time', request('end_time', '12:00')) }}',

                init() {
                    this.$nextTick(() => {
                        this.evaluateScroll();
                    });
                },

                evaluateScroll() {
                    const el = this.$refs.termsContainer;
                    if (!el) return;
                    if (el.scrollHeight - el.clientHeight <= 25) {
                        this.scrolledToBottom = true;
                    }
                },

                checkScrolled(e) {
                    if (this.scrolledToBottom) return;
                    const el = e.target;
                    if (el.scrollHeight - el.scrollTop - el.clientHeight <= 30) {
                        this.scrolledToBottom = true;
                    }
                },

                scrollToBottom() {
                    const el = this.$refs.termsContainer;
                    if (el) {
                        el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
                        setTimeout(() => {
                            this.scrolledToBottom = true;
                        }, 350);
                    }
                },

                proceedToForm() {
                    if (!this.agreed) return;
                    this.step = 2;
                    this.$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
                },

                get totalDays() {
                    if (!this.startDate) return 1;
                    if (!this.endDate || this.endDate < this.startDate) return 1;
                    const s = new Date(this.startDate + 'T00:00:00');
                    const e = new Date(this.endDate   + 'T00:00:00');
                    return Math.max(1, Math.round((e - s) / (1000 * 60 * 60 * 24)) + 1);
                },

                get durationHours() {
                    if (!this.startTime || !this.endTime) return 0;
                    const start = new Date(`2000-01-01T${this.startTime}`);
                    const end   = new Date(`2000-01-01T${this.endTime}`);
                    if (end <= start) return 0;
                    return (end - start) / (1000 * 60 * 60);
                },

                get durationSummary() {
                    const days = this.totalDays;
                    const dayStr = days === 1 ? '1 day' : `${days} days`;
                    if (this.billingType === 'daily') {
                        return `${dayStr} @ ₱${this.dailyRate.toFixed(2)}/day`;
                    }
                    const hours = this.durationHours;
                    return `${hours} hrs/day × ${dayStr} @ ₱${this.hourlyRate.toFixed(2)}/hr`;
                },

                get estimatedTotal() {
                    const days = this.totalDays;
                    if (this.billingType === 'daily') {
                        return parseFloat((days * this.dailyRate).toFixed(2));
                    }
                    const hours = this.durationHours;
                    return parseFloat((hours * this.hourlyRate * days).toFixed(2));
                }
            }))
        })
    </script>
</body>
</html>
