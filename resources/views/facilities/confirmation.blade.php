<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Submitted - EVSU Ormoc</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <style>
        .font-display { font-family: 'Franklin Gothic Medium', 'Franklin Gothic', 'Arial Black', sans-serif; }
        .font-body { font-family: 'Lucida Fax', 'Georgia', serif; }
        body { font-family: 'Lucida Fax', 'Georgia', serif; }
        .bg-warm-radial { background: radial-gradient(circle at top left, #f8f3ed 0%, #e8dbcb 45%, #c2a889 100%); }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-[#504538] font-body flex flex-col">

    <header class="bg-[#334c42] text-white py-4 shadow-md text-center">
        <h1 class="font-display tracking-widest uppercase text-sm md:text-base">EVSU Lodging & Conference Center</h1>
    </header>

    <main class="flex-grow flex items-center justify-center p-4 py-12">
        <div class="bg-white rounded-3xl shadow-2xl border border-[#e8dbcb] p-8 md:p-12 max-w-2xl w-full text-center">
            
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 text-green-600 text-5xl shadow-inner">
                <i class="fa-solid fa-check"></i>
            </div>
            
            <h2 class="text-3xl font-display text-[#334c42] mb-2">Booking Request Submitted!</h2>
            <p class="text-[#627e71] mb-8 text-lg">Thank you, {{ $reservation->booker_name }}. Your request is currently pending admin approval.</p>
            
            <div class="bg-[#f8f3ed] rounded-2xl p-6 border border-[#d8c3ab] text-left mb-8">
                <div class="flex justify-between items-start border-b border-[#d8c3ab] pb-4 mb-4">
                    <div>
                        <div class="text-sm font-bold text-[#827567] uppercase tracking-wider mb-1">Reference Number</div>
                        <div class="text-2xl font-display text-[#334c42]">{{ $reservation->reference_number }}</div>
                    </div>
                    <span class="inline-block px-4 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-bold uppercase tracking-wider shadow-sm border border-yellow-200">
                        {{ $reservation->status }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <div class="font-bold text-[#827567] mb-1">Facility</div>
                        <div class="text-[#504538] font-bold">{{ $reservation->facility->name }}</div>
                    </div>
                    <div>
                        <div class="font-bold text-[#827567] mb-1">Date</div>
                        <div class="text-[#504538] font-bold">{{ $reservation->reservation_date->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <div class="font-bold text-[#827567] mb-1">Time</div>
                        <div class="text-[#504538] font-bold">{{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}</div>
                    </div>
                    <div>
                        <div class="font-bold text-[#827567] mb-1">Estimated Amount</div>
                        <div class="text-[#334c42] font-bold text-base">₱{{ number_format($reservation->estimated_amount, 2) }}</div>
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-[#827567] mb-8 italic">
                You will receive a confirmation email at <strong>{{ $reservation->booker_email }}</strong> once our team reviews and approves your request. Please save your reference number.
            </p>
            
            <a href="{{ route('home') }}#facilities" class="inline-block bg-[#334c42] hover:bg-[#253930] text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-md">
                Return to Facilities
            </a>
            
        </div>
    </main>
    
</body>
</html>
