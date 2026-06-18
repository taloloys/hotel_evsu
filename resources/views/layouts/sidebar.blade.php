<div class="p-4">

    {{-- FRONT DESK --}}
    @if(request()->is('frontdesk*') || request()->routeIs('frontdesk.*'))

        @include('layouts.sidebar.frontdesk')

    {{-- ACCOUNTING --}}
    @elseif(request()->is('accounting*') || request()->routeIs('accounting.*'))

        @include('layouts.sidebar.accounting')

    {{-- COFFEE SHOP --}}
    @elseif(request()->is('coffeeshop*') || request()->routeIs('coffeeshop.*'))

        @include('layouts.sidebar.coffeeshop')

    {{-- ADMIN --}}
    @elseif(request()->is('admin*') || request()->routeIs('admin.*'))

        @include('layouts.sidebar.admin')

    @endif

</div>