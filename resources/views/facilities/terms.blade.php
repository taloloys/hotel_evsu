<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility Terms & Conditions - EVSU Ormoc</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    @include('partials.pwa')

    <style>
        .font-display { font-family: 'Franklin Gothic Medium', 'Franklin Gothic', 'Arial Black', sans-serif; }
        .font-body { font-family: 'Lucida Fax', 'Georgia', serif; }
        body { font-family: 'Lucida Fax', 'Georgia', serif; }
        .glass-header { background: rgba(194, 168, 137, 0.95); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        .bg-warm-radial { background: radial-gradient(circle at top left, #f8f3ed 0%, #e8dbcb 45%, #c2a889 100%); }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-[#504538] font-body flex flex-col">

    <header class="glass-header sticky top-0 z-50 border-b border-[#827567]/30 shadow-sm">
        <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}#facilities" class="group flex items-center gap-3">
                <div class="h-10 w-10 bg-[#334c42] rounded-full flex items-center justify-center text-white transition-transform group-hover:-translate-x-1">
                    <i class="fa-solid fa-arrow-left"></i>
                </div>
                <span class="font-bold text-[#504538]">Back to Facilities</span>
            </a>
        </div>
    </header>

    <main class="flex-grow py-12 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl bg-white rounded-3xl shadow-xl overflow-hidden border border-[#e8dbcb]">
            <div class="bg-[#334c42] p-8 text-center border-b-4 border-[#c2a889]">
                <h1 class="text-3xl font-display text-white tracking-wider">Facility Terms & Conditions</h1>
            </div>
            
            <div class="p-8 md:p-12 text-[#504538] leading-relaxed">
                {!! nl2br(e($termsContent)) !!}
            </div>
        </div>
    </main>

    <footer class="bg-[#334c42] text-[#e8dbcb] py-8 text-center text-sm border-t-4 border-[#c2a889]">
        <p>&copy; {{ date('Y') }} EVSU Ormoc - Hotel. All rights reserved.</p>
    </footer>
</body>
</html>
