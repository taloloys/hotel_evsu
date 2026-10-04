<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Request Submitted - EVSU Ormoc</title>
    <meta name="description" content="Your facility booking request has been submitted and is pending admin approval.">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <style>
        .font-display { font-family: 'Franklin Gothic Medium', 'Franklin Gothic', 'Arial Black', sans-serif; }
        .font-body    { font-family: 'Lucida Fax', 'Georgia', serif; }
        body          { font-family: 'Lucida Fax', 'Georgia', serif; }
        .bg-warm-radial { background: radial-gradient(ellipse at top, #f8f3ed 0%, #e8dbcb 50%, #c2a889 100%); }

        /* Checkmark animation */
        @keyframes scaleIn {
            from { transform: scale(0) rotate(-15deg); opacity: 0; }
            to   { transform: scale(1) rotate(0deg);  opacity: 1; }
        }
        @keyframes fadeUp {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        .anim-check  { animation: scaleIn 0.5s cubic-bezier(.22,.61,.36,1.4) 0.15s both; }
        .anim-fade-1 { animation: fadeUp 0.5s ease 0.35s both; }
        .anim-fade-2 { animation: fadeUp 0.5s ease 0.50s both; }
        .anim-fade-3 { animation: fadeUp 0.5s ease 0.65s both; }
        .anim-fade-4 { animation: fadeUp 0.5s ease 0.80s both; }
        .anim-fade-5 { animation: fadeUp 0.5s ease 0.95s both; }

        /* Pending badge pulse */
        @keyframes pendingPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(202, 138, 4, 0.4); }
            50%       { box-shadow: 0 0 0 6px rgba(202, 138, 4, 0);  }
        }
        .pending-badge { animation: pendingPulse 2s ease-in-out infinite; }

        /* Process steps */
        .process-step { display: flex; align-items: flex-start; gap: 0.875rem; padding: 0.75rem 0; }
        .process-step:not(:last-child) { border-bottom: 1px dashed #e8dbcb; }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-[#504538] font-body flex flex-col">

    <header class="bg-[#334c42] text-white py-4 shadow-md text-center">
        <h1 class="font-display tracking-widest uppercase text-sm md:text-base">EVSU Lodging &amp; Conference Center</h1>
    </header>

    <main class="flex-grow flex items-center justify-center p-4 py-12">
        <div class="bg-white rounded-3xl shadow-2xl border border-[#e8dbcb] p-8 md:p-12 max-w-2xl w-full">

            {{-- ── Success icon ── --}}
            <div class="flex justify-center mb-6 anim-check">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center shadow-inner border-4 border-green-200">
                    <i class="fa-solid fa-check text-green-600 text-4xl"></i>
                </div>
            </div>

            {{-- ── Heading ── --}}
            <div class="text-center anim-fade-1">
                <h2 class="text-3xl font-display text-[#334c42] mb-2">Booking Request Submitted!</h2>
                <p class="text-[#627e71] text-base">
                    Thank you, <strong>{{ $reservation->booker_name }}</strong>. Your request has been received and is currently <strong>awaiting admin approval</strong>.
                </p>
            </div>

            {{-- ── PENDING status callout ── --}}
            <div class="mt-6 bg-yellow-50 border-2 border-yellow-300 rounded-2xl p-5 flex items-start gap-4 anim-fade-2">
                <div class="w-10 h-10 rounded-full bg-yellow-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-hourglass-half text-yellow-700 text-base"></i>
                </div>
                <div>
                    <div class="font-bold text-yellow-800 text-sm mb-1 uppercase tracking-wide">Request Status: PENDING</div>
                    <p class="text-yellow-700 text-sm leading-relaxed">
                        Your booking is <strong>not yet confirmed</strong>. It will only be confirmed once the Front Desk or Admin reviews and approves it. You will be notified via email once a decision has been made.
                    </p>
                </div>
            </div>

            {{-- ── Reservation details card ── --}}
            <div class="mt-6 bg-[#f8f3ed] rounded-2xl border border-[#d8c3ab] overflow-hidden anim-fade-3">
                {{-- Card header: reference + badge --}}
                <div class="flex justify-between items-center px-6 py-4 border-b border-[#d8c3ab]">
                    <div>
                        <div class="text-xs font-bold text-[#827567] uppercase tracking-widest mb-1">Reference Number</div>
                        <div class="text-2xl font-display text-[#334c42]">{{ $reservation->reference_number }}</div>
                    </div>
                    <span class="pending-badge inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-bold uppercase tracking-wider border border-yellow-300">
                        <i class="fa-solid fa-clock"></i>
                        {{ strtoupper($reservation->status) }}
                    </span>
                </div>

                {{-- Details grid --}}
                <div class="grid grid-cols-2 gap-y-4 gap-x-6 px-6 py-5 text-sm">
                    <div>
                        <div class="font-bold text-[#827567] mb-0.5">Facility</div>
                        <div class="text-[#504538] font-bold">
                            {{ $reservation->facility_name }}
                            @if($reservation->isConsolidated())
                                <span class="inline-block ml-1 px-2 py-0.5 text-[11px] rounded-full bg-[#334c42]/10 text-[#334c42] font-semibold">Consolidated Set</span>
                            @endif
                        </div>
                        @if($reservation->isConsolidated() && $reservation->all_facilities->isNotEmpty())
                            <div class="text-xs text-[#827567] mt-1">
                                <strong>Includes:</strong> {{ $reservation->all_facilities->pluck('name')->join(', ') }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-[#827567] mb-0.5">Date</div>
                        <div class="text-[#504538] font-bold">{{ $reservation->reservation_date->format('M d, Y') }}</div>
                        @if($reservation->end_date && $reservation->end_date->ne($reservation->reservation_date))
                            <div class="text-xs text-[#827567]">to {{ $reservation->end_date->format('M d, Y') }}</div>
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-[#827567] mb-0.5">Time</div>
                        <div class="text-[#504538] font-bold">
                            {{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-[#827567] mb-0.5">Estimated Amount</div>
                        <div class="text-[#334c42] font-bold text-base">₱{{ number_format($reservation->estimated_amount, 2) }}</div>
                        <div class="text-xs text-[#827567]">Subject to final admin confirmation</div>
                    </div>
                </div>
            </div>

            {{-- ── What happens next ── --}}
            <div class="mt-6 anim-fade-4">
                <div class="text-xs font-bold text-[#827567] uppercase tracking-widest mb-3">What Happens Next</div>
                <div class="bg-white rounded-xl border border-[#e8dbcb] px-5 divide-y divide-[#f0e8df]">
                    <div class="process-step">
                        <div class="w-7 h-7 rounded-full bg-[#334c42]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-magnifying-glass text-[#334c42] text-xs"></i>
                        </div>
                        <div class="text-sm text-[#504538]">
                            <strong>Admin reviews</strong> your booking request for availability and completeness.
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="w-7 h-7 rounded-full bg-[#334c42]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-envelope text-[#334c42] text-xs"></i>
                        </div>
                        <div class="text-sm text-[#504538]">
                            You'll receive a confirmation email at <strong>{{ $reservation->booker_email }}</strong> once your request is approved or rejected.
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="w-7 h-7 rounded-full bg-[#334c42]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-bookmark text-[#334c42] text-xs"></i>
                        </div>
                        <div class="text-sm text-[#504538]">
                            Keep your reference number <strong>{{ $reservation->reference_number }}</strong> for follow-ups.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── CTA ── --}}
            <div class="mt-8 flex flex-col sm:flex-row gap-3 anim-fade-5">
                <a href="{{ route('home') }}#facilities"
                   class="flex-1 inline-flex items-center justify-center gap-2 bg-[#334c42] hover:bg-[#253930] text-white font-bold py-3.5 px-8 rounded-xl transition-colors shadow-md">
                    <i class="fa-solid fa-building-columns"></i>
                    Return to Facilities
                </a>
            </div>

        </div>
    </main>

</body>
</html>
