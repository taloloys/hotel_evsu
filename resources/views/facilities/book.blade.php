<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book {{ $facility->name }} - EVSU Ormoc</title>

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
            <a href="{{ route('facilities.show', $facility) }}" class="flex items-center gap-2 hover:text-[#c2a889] transition-colors font-bold text-sm">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
            <h1 class="font-display tracking-wide uppercase text-sm md:text-base">Booking Request</h1>
            <div class="w-16"></div> <!-- spacer -->
        </div>
    </header>

    <main class="py-10">
        <div class="mx-auto max-w-4xl px-4">
            
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-display text-[#334c42] mb-2">{{ $facility->name }}</h2>
                <div class="text-[#627e71] font-bold">₱{{ number_format($facility->rate, 2) }} / {{ ucfirst($facility->rate_type) }}</div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0"><i class="fa-solid fa-circle-exclamation text-red-500"></i></div>
                        <div class="ml-3">
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
                    <form action="{{ route('facilities.submit', $facility) }}" method="POST" class="bg-white rounded-2xl shadow-lg border border-[#e8dbcb] p-6 md:p-8"
                          x-data="bookingCalculator({{ $facility->rate }}, '{{ $facility->rate_type }}')">
                        @csrf
                        
                        <h3 class="text-xl font-display text-[#334c42] border-b border-[#e8dbcb] pb-3 mb-6">Booker Information</h3>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-[#504538] mb-1">Full Name</label>
                                <input type="text" name="booker_name" value="{{ old('booker_name') }}" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">Email Address</label>
                                    <input type="email" name="booker_email" value="{{ old('booker_email') }}" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">Contact Number</label>
                                    <input type="text" name="booker_contact" value="{{ old('booker_contact') }}" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                            </div>
                        </div>

                        <h3 class="text-xl font-display text-[#334c42] border-b border-[#e8dbcb] pb-3 mt-8 mb-6">Reservation Schedule</h3>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-[#504538] mb-1">Date</label>
                                <input type="date" name="reservation_date" min="{{ now()->format('Y-m-d') }}" value="{{ old('reservation_date', request('date', now()->format('Y-m-d'))) }}" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">Start Time</label>
                                    <input type="time" name="start_time" x-model="startTime" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-[#504538] mb-1">End Time</label>
                                    <input type="time" name="end_time" x-model="endTime" required class="w-full rounded-lg border-[#d8c3ab] bg-[#f8f3ed] p-3 text-[#504538] focus:border-[#334c42] focus:ring focus:ring-[#334c42]/20">
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 bg-[#334c42] text-white p-5 rounded-xl flex justify-between items-center shadow-inner">
                            <div class="font-bold">Estimated Total</div>
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
                                Submit Booking Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingCalculator', (rate, rateType) => ({
                startTime: '{{ old('start_time', request('start_time', '08:00')) }}',
                endTime: '{{ old('end_time', request('end_time', '10:00')) }}',
                
                get estimatedTotal() {
                    if (rateType === 'daily') {
                        return parseFloat(rate);
                    }
                    
                    if (!this.startTime || !this.endTime) return 0;
                    
                    const start = new Date(`2000-01-01T${this.startTime}`);
                    const end = new Date(`2000-01-01T${this.endTime}`);
                    
                    if (end <= start) return 0; // invalid time
                    
                    const hours = (end - start) / (1000 * 60 * 60);
                    return parseFloat((hours * rate).toFixed(2));
                }
            }))
        })
    </script>
</body>
</html>
