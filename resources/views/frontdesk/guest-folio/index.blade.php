@extends('layouts.app')

@section('title', 'Guest Folio - Don Felipe Hotel')
@section('pageTitle', 'Guest Folio')
@section('pageSubtitle', 'View, search and manage all guest folios.')

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <span class="fw-semibold">Please correct the following errors:</span>
            <ul class="mb-0 mt-1 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('frontdesk.guest-folio') }}" id="filterForm">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="search">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                id="search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Guest name, folio #, or room #"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold" for="folio_type">Folio Type</label>
                        <select class="form-select" id="folio_type" name="folio_type">
                            <option value="ALL" @selected($folioType === 'ALL')>All Types</option>
                            <option value="GUEST" @selected($folioType === 'GUEST')>Guest</option>
                            <option value="HOUSE" @selected($folioType === 'HOUSE')>House</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold" for="status">Folio Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="ALL" @selected($statusFilter === 'ALL')>All</option>
                            <option value="OPEN" @selected($statusFilter === 'OPEN')>Open</option>
                            <option value="CLOSED" @selected($statusFilter === 'CLOSED')>Closed</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        @if($search || $folioType !== 'ALL' || $statusFilter !== 'ALL')
                            <a href="{{ route('frontdesk.guest-folio') }}" class="btn btn-outline-secondary flex-fill">
                                <i class="fa-solid fa-xmark me-1"></i> Clear
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline-secondary flex-fill" onclick="printFolioList()">
                            <i class="fa-solid fa-print me-1"></i> Print
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Results Summary --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">
            Showing <strong>{{ $folios->total() }}</strong> folio(s)
            @if($search) for "<strong>{{ $search }}</strong>" @endif
        </p>
        @if($folios->hasPages())
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                Page {{ $folios->currentPage() }} of {{ $folios->lastPage() }}
            </span>
        @endif
    </div>

    {{-- Folio Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="folioTable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Folio No.</th>
                            <th class="py-3">Guest Name</th>
                            <th class="py-3">Room</th>
                            <th class="py-3 text-center">Type</th>
                            <th class="py-3">Arrival</th>
                            <th class="py-3">Departure</th>
                            <th class="py-3 text-end">Base Rate</th>
                            <th class="py-3 text-end">Net Rate</th>
                            <th class="py-3 text-center">Payment</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($folios as $folio)
                            @php
                                $booking     = $folio->bookings->sortByDesc('booking_id')->first();
                                $room        = $booking?->room;
                                $totalCharge = $folio->total_charges;
                                $totalCredit = $folio->total_credits;
                                $balance     = $folio->balance;
                            @endphp
                            <tr>
                                <td class="px-4">
                                    <span class="fw-semibold text-primary">{{ $folio->folio_number }}</span>
                                </td>
                                <td>
                                    @if($folio->guest)
                                        <div class="fw-semibold">{{ $folio->guest->last_name }}, {{ $folio->guest->first_name }}</div>
                                        <div class="text-muted small">{{ $folio->guest->contact_number ?: '' }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($room)
                                        <span class="fw-semibold">{{ $room->room_number }}</span>
                                        <div class="text-muted small">{{ $room->room_type }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border">{{ $folio->folio_type }}</span>
                                </td>
                                <td class="small">
                                    {{ $booking?->arrival_date?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="small">
                                    {{ $booking?->departure_date?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="text-end small">
                                    @if($room)
                                        ₱{{ number_format($room->base_rate, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end small">
                                    @if($folio->net_rate !== null)
                                        <span class="text-success fw-semibold">₱{{ number_format($folio->net_rate, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center small">
                                    @if($folio->payment_method)
                                        {{ $folio->payment_method === 'Cash' ? '💵' : '💳' }}
                                        {{ $folio->payment_method }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($folio->status === 'OPEN')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Open</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Closed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#folioModal{{ $folio->folio_id }}"
                                        title="View folio details"
                                    >
                                        <i class="fa-solid fa-eye me-1"></i> Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-3 d-block"></i>
                                    @if($search)
                                        No folios found matching "<strong>{{ $search }}</strong>".
                                    @else
                                        No folios found.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($folios->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $folios->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Folio Detail Modals --}}
@foreach($folios as $folio)
    @php
        $booking     = $folio->bookings->sortByDesc('booking_id')->first();
        $room        = $booking?->room;
        $totalCharge = $folio->total_charges;
        $totalCredit = $folio->total_credits;
        $balance     = $folio->balance;
    @endphp
    <div class="modal fade" id="folioModal{{ $folio->folio_id }}" tabindex="-1" aria-labelledby="folioModalLabel{{ $folio->folio_id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold" id="folioModalLabel{{ $folio->folio_id }}">
                            <i class="fa-solid fa-file-invoice me-2 text-primary"></i>
                            Folio {{ $folio->folio_number }}
                        </h5>
                        <small class="text-muted">
                            {{ $folio->status === 'OPEN' ? '🟢 Open' : '⚫ Closed' }}
                            &bull; {{ $folio->folio_type }} Folio
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- Guest & Stay Info --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">GUEST</p>
                            <p class="mb-0 fw-semibold">
                                @if($folio->guest)
                                    {{ $folio->guest->last_name }}, {{ $folio->guest->first_name }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">MOBILE</p>
                            <p class="mb-0">{{ $folio->guest?->contact_number ?: '—' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">ADDRESS</p>
                            <p class="mb-0">{{ $folio->guest?->address_line1 ?: '—' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">PAYMENT METHOD</p>
                            <p class="mb-0">
                                @if($folio->payment_method)
                                    {{ $folio->payment_method === 'Cash' ? '💵' : '💳' }} {{ $folio->payment_method }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">ROOM</p>
                            <p class="mb-0 fw-semibold">
                                @if($room)
                                    {{ $room->room_number }}
                                    <span class="text-muted fw-normal small">{{ $room->room_type }}</span>
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">ARRIVAL</p>
                            <p class="mb-0">{{ $booking?->arrival_date?->format('M d, Y') ?? '—' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">DEPARTURE</p>
                            <p class="mb-0">{{ $booking?->departure_date?->format('M d, Y') ?? '—' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">NO. OF PAX</p>
                            <p class="mb-0">{{ $folio->num_pax }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">ROOM BASE RATE</p>
                            <p class="mb-0">
                                @if($room)
                                    ₱{{ number_format($room->base_rate, 2) }} / night
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">NET RATE (AGREED)</p>
                            <p class="mb-0">
                                @if($folio->net_rate !== null)
                                    <span class="text-success fw-semibold">₱{{ number_format($folio->net_rate, 2) }} / night</span>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">MARKET SEGMENT</p>
                            <p class="mb-0">{{ $folio->market_segment }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted small fw-semibold">JOINER</p>
                            <p class="mb-0">{{ $folio->has_joiner ? 'Yes' : 'No' }}</p>
                        </div>
                        @if($folio->special_arrangements)
                            <div class="col-md-12">
                                <p class="mb-1 text-muted small fw-semibold">SPECIAL ARRANGEMENTS</p>
                                <p class="mb-0">{{ $folio->special_arrangements }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Front Desk Controls Panel --}}
                    <div class="card border-0 bg-light mb-4 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-sliders text-primary me-2"></i>Front Desk Controls</h6>
                            <div class="row g-3">

                                {{-- Left Column --}}
                                <div class="col-lg-6">

                                    {{-- ================= Current Status ================= --}}
                                    @if($booking)
                                    <div class="card border mb-3 shadow-sm">
                                        <div class="card-header bg-white py-2">
                                            <strong>Current Status</strong>
                                        </div>

                                        <div class="card-body">

                                            <div class="d-flex justify-content-between align-items-center">

                                                <span class="text-muted">
                                                    Booking Status
                                                </span>

                                                @if($booking->status === 'CHECKED_IN')

                                                    <span class="badge bg-success">
                                                        Checked In
                                                    </span>

                                                @elseif($booking->status === 'RESERVED')

                                                    <span class="badge bg-warning text-dark">
                                                        Reserved
                                                    </span>

                                                @elseif($booking->status === 'CHECKED_OUT')

                                                    <span class="badge bg-secondary">
                                                        Checked Out
                                                    </span>

                                                @else

                                                    <span class="badge bg-danger">
                                                        Cancelled
                                                    </span>

                                                @endif

                                            </div>

                                            @if($room)

                                            <hr>

                                            <div class="small text-muted">
                                                Assigned Room
                                            </div>

                                            <div class="fw-semibold">
                                                Room {{ $room->room_number }}
                                            </div>

                                            @endif

                                        </div>
                                    </div>
                                    @endif


                                    {{-- ================= Guest Actions ================= --}}
                                    <div class="card border mb-3 shadow-sm">

                                        <div class="card-header bg-white py-2">
                                            <strong>Guest Actions</strong>
                                        </div>

                                        <div class="card-body">

                                            <div class="d-flex flex-wrap gap-2">

                                                @if($folio->status === 'OPEN')

                                                    @if(!$folio->isSettled())
                                                        <button
                                                            type="button"
                                                            class="btn btn-success btn-sm"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#markPaidCollapse{{ $folio->folio_id }}">
                                                            <i class="fa-solid fa-circle-check me-1"></i>
                                                            Mark Paid
                                                        </button>

                                                        <button
                                                            type="button"
                                                            class="btn btn-info btn-sm text-white"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#chargeAccountCollapse{{ $folio->folio_id }}">
                                                            <i class="fa-solid fa-building me-1"></i>
                                                            Charge to Account
                                                        </button>
                                                    @endif

                                                    <form
                                                        method="POST"
                                                        action="{{ route('frontdesk.guest-folio.close',$folio->folio_id) }}">
                                                        @csrf
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="event.preventDefault(); Swal.fire({ title: 'Are you sure?', text: 'Are you sure you want to close this folio?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, close it!' }).then((result) => { if (result.isConfirmed) { const f = this.closest('form'); if(f.requestSubmit){ f.requestSubmit(); }else{ f.submit(); } } });">
                                                            <i class="fa-solid fa-lock me-1"></i>
                                                            Close Folio
                                                        </button>
                                                    </form>

                                                @else

                                                    <form
                                                        method="POST"
                                                        action="{{ route('frontdesk.guest-folio.reopen',$folio->folio_id) }}">

                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="btn btn-success btn-sm">

                                                            <i class="fa-solid fa-lock-open me-1"></i>
                                                            Reopen Folio

                                                        </button>

                                                    </form>

                                                @endif


                                                @if($folio->status === 'OPEN' && $booking && $booking->status=='RESERVED')

                                                    <form
                                                        method="POST"
                                                        action="{{ route('frontdesk.guest-folio.checkin',$booking->booking_id) }}">

                                                        @csrf

                                                        <button
                                                            type="button"
                                                            class="btn btn-primary btn-sm"
                                                            onclick="swalConfirmCheckIn(this, '{{ $folio->guest?->first_name }}', '{{ $room?->room_number }}')">

                                                            <i class="fa-solid fa-door-open me-1"></i>
                                                            Check In

                                                        </button>

                                                    </form>

                                                @endif


                                                @if($folio->status === 'OPEN' && $booking && $booking->status=='CHECKED_IN')

                                                    <button
                                                        type="button"
                                                        class="btn btn-warning btn-sm"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#checkoutFormCollapse{{ $folio->folio_id }}">

                                                        <i class="fa-solid fa-door-closed me-1"></i>
                                                        Check Out

                                                    </button>

                                                @endif

                                            </div>

                                             <div class="accordion" id="guestActionsAccordion{{ $folio->folio_id }}">

                                             {{-- Mark Paid Form --}}
                                             @if($folio->status === 'OPEN')
                                             <div class="collapse mt-3" id="markPaidCollapse{{ $folio->folio_id }}" data-bs-parent="#guestActionsAccordion{{ $folio->folio_id }}">
                                                 <div class="border rounded p-3">
                                                     <h6 class="fw-bold mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i> Mark Folio as Paid</h6>
                                                     <form method="POST" action="{{ route('frontdesk.guest-folio.mark-paid', $folio->folio_id) }}">
                                                         @csrf
                                                         <div class="bg-light rounded p-2 mb-3 border">
                                                             <div class="d-flex justify-content-between fw-bold">
                                                                 <span>Outstanding Balance:</span>
                                                                 <span class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                                                     ₱{{ number_format(abs($balance), 2) }}
                                                                 </span>
                                                             </div>
                                                         </div>
                                                         <div class="mb-3">
                                                             <label class="form-label fw-semibold" for="paid_amount_{{ $folio->folio_id }}">Payment Amount (Full Balance) <span class="text-danger">*</span></label>
                                                             <div class="input-group">
                                                                 <span class="input-group-text">₱</span>
                                                                 <input type="number" class="form-control bg-light" id="paid_amount_{{ $folio->folio_id }}" name="amount" value="{{ number_format(max($balance, 0), 2, '.', '') }}" min="0.01" step="0.01" required readonly>
                                                             </div>
                                                         </div>
                                                         <div class="mb-3">
                                                             <label class="form-label fw-semibold" for="paid_method_{{ $folio->folio_id }}">Payment Method <span class="text-danger">*</span></label>
                                                             <select class="form-select" id="paid_method_{{ $folio->folio_id }}" name="payment_method" required>
                                                                 <option value="Cash" @selected(($folio->payment_method ?? 'Cash') === 'Cash')>💵 Cash</option>
                                                                 <option value="Credit Card" @selected($folio->payment_method === 'Credit Card')>💳 Credit Card</option>
                                                             </select>
                                                         </div>
                                                         <div class="mb-3">
                                                             <label class="form-label fw-semibold" for="paid_notes_{{ $folio->folio_id }}">Reference Notes</label>
                                                             <input type="text" class="form-control" id="paid_notes_{{ $folio->folio_id }}" name="reference_notes" placeholder="e.g. receipt #, bank ref, etc.">
                                                         </div>
                                                         <div class="form-check mb-3">
                                                             <input class="form-check-input" type="checkbox" name="close_folio" id="close_folio_{{ $folio->folio_id }}" value="1">
                                                             <label class="form-check-label fw-semibold" for="close_folio_{{ $folio->folio_id }}">Close folio after payment</label>
                                                         </div>
                                                         <div class="text-end">
                                                             <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#markPaidCollapse{{ $folio->folio_id }}">Cancel</button>
                                                             <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-check me-1"></i> Confirm Payment</button>
                                                         </div>
                                                     </form>
                                                 </div>
                                             </div>

                                             {{-- Charge Account Form --}}
                                             <div class="collapse mt-3" id="chargeAccountCollapse{{ $folio->folio_id }}" data-bs-parent="#guestActionsAccordion{{ $folio->folio_id }}">
                                                 <div class="border rounded p-3">
                                                     <h6 class="fw-bold mb-3"><i class="fa-solid fa-building text-info me-2"></i> Charge to Account</h6>
                                                     <form method="POST" action="{{ route('frontdesk.guest-folio.charge-account', $folio->folio_id) }}">
                                                         @csrf
                                                         <div class="bg-light rounded p-2 mb-3 border">
                                                             <div class="d-flex justify-content-between fw-bold">
                                                                 <span>Outstanding Balance:</span>
                                                                 <span class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                                                     ₱{{ number_format(abs($balance), 2) }}
                                                                 </span>
                                                             </div>
                                                         </div>
                                                         <div class="mb-3">
                                                             <label class="form-label fw-semibold" for="charge_amount_{{ $folio->folio_id }}">Amount to Charge <span class="text-danger">*</span></label>
                                                             <div class="input-group">
                                                                 <span class="input-group-text">₱</span>
                                                                 <input type="number" class="form-control" id="charge_amount_{{ $folio->folio_id }}" name="amount" value="{{ number_format(max($balance, 0), 2, '.', '') }}" min="0.01" step="0.01" required>
                                                             </div>
                                                         </div>
                                                         <div class="mb-3">
                                                             <label class="form-label fw-semibold" for="charge_credit_account_id_{{ $folio->folio_id }}">Select Credit Account <span class="text-danger">*</span></label>
                                                             <select class="form-select charge-account-select" id="charge_credit_account_id_{{ $folio->folio_id }}" name="credit_account_id" required>
                                                                 <option value="">Select Account...</option>
                                                                 @foreach($creditAccounts as $account)
                                                                     <option value="{{ $account->account_id }}">{{ $account->account_name }} (Limit: ₱{{ number_format($account->available_credit, 2) }})</option>
                                                                 @endforeach
                                                             </select>
                                                         </div>
                                                         <div class="mb-3">
                                                             <label class="form-label fw-semibold" for="charge_notes_{{ $folio->folio_id }}">Reference Notes</label>
                                                             <input type="text" class="form-control" id="charge_notes_{{ $folio->folio_id }}" name="reference_notes" placeholder="e.g. LOA #, authorized by...">
                                                         </div>
                                                         <div class="form-check mb-3">
                                                             <input class="form-check-input" type="checkbox" name="close_folio" id="charge_close_folio_{{ $folio->folio_id }}" value="1">
                                                             <label class="form-check-label fw-semibold" for="charge_close_folio_{{ $folio->folio_id }}">Close folio after charging</label>
                                                         </div>
                                                         <div class="text-end">
                                                             <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#chargeAccountCollapse{{ $folio->folio_id }}">Cancel</button>
                                                             <button type="submit" class="btn btn-info text-white btn-sm"><i class="fa-solid fa-check me-1"></i> Confirm Charge</button>
                                                         </div>
                                                     </form>
                                                 </div>
                                             </div>
                                             @endif

                                            {{-- Checkout Form --}}
                                            @if($booking && $booking->status=='CHECKED_IN')

                                            <div
                                                class="collapse mt-3"
                                                id="checkoutFormCollapse{{ $folio->folio_id }}"
                                                data-bs-parent="#guestActionsAccordion{{ $folio->folio_id }}">

                                                <div class="border rounded p-3">

                                                    <form
                                                        method="POST"
                                                        action="{{ route('frontdesk.guest-folio.checkout',$booking->booking_id) }}">

                                                        @csrf

                                                        <div class="row g-2">

                                                            <div class="col-7">

                                                                <input
                                                                    type="text"
                                                                    name="checkout_time"
                                                                    class="form-control"
                                                                    required
                                                                    maxlength="5"
                                                                    value="{{ now()->format('g:i') }}">

                                                            </div>

                                                            <div class="col-5">

                                                                <select
                                                                    name="checkout_period"
                                                                    class="form-select"
                                                                    required>

                                                                    <option value="AM"
                                                                        @selected(now()->format('A')=='AM')>
                                                                        AM
                                                                    </option>

                                                                    <option value="PM"
                                                                        @selected(now()->format('A')=='PM')>
                                                                        PM
                                                                    </option>

                                                                </select>

                                                            </div>

                                                            <div class="col-12 text-end">

                                                                <button
                                                                    class="btn btn-danger">

                                                                    Confirm Checkout

                                                                </button>

                                                            </div>

                                                        </div>

                                                    </form>

                                                </div>

                                            </div>

                                            @endif

                                            </div>

                                        </div>

                                    </div>


                                    {{-- ================= Room Transfer ================= --}}
                                    @if($folio->status=='OPEN' && $booking && $booking->status=='CHECKED_IN')

                                    <div class="card border shadow-sm">

                                        <div class="card-header bg-white py-2">

                                            <strong>Room Transfer</strong>

                                        </div>

                                        <div class="card-body">

                                            @if($availableRooms->isEmpty())

                                                <div class="text-muted">

                                                    No available rooms.

                                                </div>

                                            @else

                                                <form
                                                    method="POST"
                                                    action="{{ route('frontdesk.guest-folio.transfer',$booking->booking_id) }}">

                                                    @csrf

                                                    <div class="mb-3">

                                                        <select
                                                            name="new_room_id"
                                                            class="form-select"
                                                            required

                                                            onchange="document.getElementById('roomTransferRate{{ $booking->booking_id }}').value=this.options[this.selectedIndex].dataset.rate">

                                                            <option selected disabled>

                                                                Select Room

                                                            </option>

                                                            @foreach($availableRooms as $availRoom)

                                                            <option
                                                                value="{{ $availRoom->room_id }}"
                                                                data-rate="{{ $availRoom->base_rate }}">

                                                                Room {{ $availRoom->room_number }}
                                                                —
                                                                {{ $availRoom->room_type }}

                                                            </option>

                                                            @endforeach

                                                        </select>

                                                    </div>

                                                    <div class="input-group mb-3">

                                                        <span class="input-group-text">
                                                            ₱
                                                        </span>

                                                        <input
                                                            id="roomTransferRate{{ $booking->booking_id }}"
                                                            name="net_rate"
                                                            type="number"
                                                            class="form-control"
                                                            value="{{ $folio->net_rate ?? '' }}"
                                                            min="0"
                                                            step="0.01">

                                                    </div>

                                                    <div class="text-end">

                                                        <button
                                                            type="button"
                                                            class="btn btn-primary"
                                                            onclick="swalConfirmTransfer(this)">

                                                            <i class="fa-solid fa-right-left me-1"></i>

                                                            Transfer Guest

                                                        </button>

                                                    </div>

                                                </form>

                                            @endif

                                        </div>

                                    </div>

                                    @endif

                                </div>

                                {{-- Right Column : Post Transaction --}}
                    <div class="col-lg-6">

                        <div class="border rounded-3 p-3 bg-white h-100">

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h6 class="fw-semibold mb-0">
                                        <i class="fa-solid fa-receipt text-primary me-2"></i>
                                        Post Transaction
                                    </h6>
                                    <small class="text-muted">
                                        Add a charge or record a payment.
                                    </small>
                                </div>
                            </div>

                            @if($folio->status === 'OPEN')

                                <form method="POST"
                                    action="{{ route('frontdesk.guest-folio.transaction', $folio->folio_id) }}">

                                    @csrf

                                    <div class="row g-3">

                                        {{-- Charge Code --}}
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold mb-1">
                                                Charge Code
                                            </label>

                                            <select
                                                name="charge_code"
                                                class="form-select"
                                                required>

                                                <option value="" disabled selected>
                                                    Select charge code
                                                </option>

                                                @foreach($chargeCodes as $code)
                                                    <option value="{{ $code->charge_code }}">
                                                        {{ $code->charge_code }}
                                                        —
                                                        {{ $code->description }}
                                                        ({{ $code->category }})
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>

                                        {{-- Type --}}
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold mb-1">
                                                Transaction
                                            </label>

                                            <select
                                                name="type"
                                                class="form-select"
                                                required>

                                                <option value="CHARGE">
                                                    Charge
                                                </option>

                                                <option value="PAYMENT">
                                                    Payment
                                                </option>

                                            </select>
                                        </div>

                                        {{-- Amount --}}
                                        <div class="col-md-8">
                                            <label class="form-label small fw-semibold mb-1">
                                                Amount
                                            </label>

                                            <div class="input-group">

                                                <span class="input-group-text">
                                                    ₱
                                                </span>

                                                <input
                                                    type="number"
                                                    name="amount"
                                                    class="form-control text-end"
                                                    min="0.01"
                                                    step="0.01"
                                                    placeholder="0.00"
                                                    required>

                                            </div>
                                        </div>

                                        {{-- Reference --}}
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold mb-1">
                                                Reference / Notes
                                            </label>

                                            <input
                                                type="text"
                                                name="reference_notes"
                                                class="form-control"
                                                placeholder="Receipt number, surcharge, discount, remarks...">
                                        </div>

                                        {{-- Button --}}
                                        <div class="col-12 d-grid">

                                            <button
                                                type="submit"
                                                class="btn btn-primary">

                                                <i class="fa-solid fa-plus me-2"></i>

                                                Post Transaction

                                            </button>

                                        </div>

                                    </div>

                                </form>

                            @else

                                <div class="alert alert-light border mb-0">

                                    <i class="fa-solid fa-lock text-secondary me-2"></i>

                                    This folio is already
                                    <strong>Closed</strong>.
                                    Reopen it before posting additional charges or payments.

                                </div>

                            @endif

                        </div>

                    </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- ====================== TRANSACTION LEDGER ====================== --}}
                    @php
                        // Categorize all transactions for this folio
                        $txnsByCategory = [
                            'room'       => collect(),
                            'restaurant' => collect(),
                            'laundry'    => collect(),
                            'tax'        => collect(),
                            'discounts'  => collect(),
                            'other'      => collect(),
                            'payments'   => collect(),
                        ];

                        $roomCodes      = [100, 103];
                        $laundryCodes   = [104, 105];
                        $restaurantCodes = [200];
                        $discountCodes  = [201];

                        foreach ($folio->transactions->sortBy('timestamp') as $txn) {
                            $code     = (int) $txn->charge_code;
                            $category = $txn->chargeCode?->category ?? 'HOTEL';

                            if ($category === 'PAYMENT') {
                                $txnsByCategory['payments']->push($txn);
                            } elseif (in_array($code, $roomCodes)) {
                                $txnsByCategory['room']->push($txn);
                            } elseif (in_array($code, $laundryCodes)) {
                                $txnsByCategory['laundry']->push($txn);
                            } elseif (in_array($code, $restaurantCodes)) {
                                $txnsByCategory['restaurant']->push($txn);
                            } elseif ($category === 'TAX_SERVICE') {
                                $txnsByCategory['tax']->push($txn);
                            } elseif (in_array($code, $discountCodes)) {
                                $txnsByCategory['discounts']->push($txn);
                            } else {
                                $txnsByCategory['other']->push($txn);
                            }
                        }

                        $sumRoom       = $txnsByCategory['room']->sum('charge_amount');
                        $sumRestaurant = $txnsByCategory['restaurant']->sum('charge_amount');
                        $sumLaundry    = $txnsByCategory['laundry']->sum('charge_amount');
                        $sumTax        = $txnsByCategory['tax']->sum('charge_amount');
                        $sumDiscounts  = $txnsByCategory['discounts']->sum('charge_amount');
                        $sumOther      = $txnsByCategory['other']->sum('charge_amount');
                        $sumPayments   = $txnsByCategory['payments']->sum('credit_amount');
                    @endphp

                    {{-- Folio Summary Cards --}}
                    <div class="row g-2 mb-3">
                        @php
                            $summaryItems = [
                                ['label' => 'Room Charges',         'icon' => 'fa-bed',             'color' => 'primary',  'amount' => $sumRoom],
                                ['label' => 'Restaurant / F&B',     'icon' => 'fa-utensils',        'color' => 'warning',  'amount' => $sumRestaurant],
                                ['label' => 'Laundry',              'icon' => 'fa-shirt',           'color' => 'info',     'amount' => $sumLaundry],
                                ['label' => 'Taxes & Fees',         'icon' => 'fa-landmark',        'color' => 'secondary','amount' => $sumTax],
                                ['label' => 'Discounts',            'icon' => 'fa-tag',             'color' => 'success',  'amount' => $sumDiscounts],
                                ['label' => 'Other Charges',        'icon' => 'fa-circle-dot',      'color' => 'dark',     'amount' => $sumOther],
                                ['label' => 'Total Payments',       'icon' => 'fa-money-bill-wave', 'color' => 'success',  'amount' => $sumPayments, 'isPayment' => true],
                            ];
                        @endphp
                        @foreach($summaryItems as $item)
                            @if($item['amount'] > 0)
                            <div class="col-6 col-md-4">
                                <div class="border rounded-3 p-2 d-flex align-items-center gap-2 bg-white h-100">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:36px;height:36px;background:var(--bs-{{ $item['color'] }}-bg-subtle,#f8f9fa);">
                                        <i class="fa-solid {{ $item['icon'] }} text-{{ $item['color'] }} small"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-muted" style="font-size:0.7rem;line-height:1.2;">{{ $item['label'] }}</div>
                                        <div class="fw-bold small {{ isset($item['isPayment']) ? 'text-success' : 'text-dark' }}">
                                            ₱{{ number_format($item['amount'], 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Outstanding Balance Banner --}}
                    @if($balance > 0)
                    <div class="alert alert-danger border-0 py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-circle-exclamation me-2"></i><strong>Outstanding Balance</strong></span>
                        <span class="fw-bold fs-5">₱{{ number_format($balance, 2) }}</span>
                    </div>
                    @elseif($balance < 0)
                    <div class="alert alert-warning border-0 py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-triangle-exclamation me-2"></i><strong>Overpaid — Refund Due</strong></span>
                        <span class="fw-bold fs-5">₱{{ number_format(abs($balance), 2) }}</span>
                    </div>
                    @else
                    <div class="alert alert-success border-0 py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-circle-check me-2"></i><strong>Fully Settled</strong></span>
                        <span class="fw-bold fs-5">₱0.00</span>
                    </div>
                    @endif

                    {{-- Categorized Transaction Ledger --}}
                    <div class="border rounded-3">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-receipt text-primary me-2"></i>
                                Transaction Ledger
                            </h6>
                            <small class="text-muted">{{ $folio->transactions->count() }} Transaction(s)</small>
                        </div>

                        @if($folio->transactions->isEmpty())
                            <div class="py-5 text-center">
                                <i class="fa-solid fa-file-invoice text-muted mb-3 fs-2"></i>
                                <p class="text-muted mb-0">No transactions have been recorded for this folio.</p>
                            </div>
                        @else
                            @php
                                $ledgerSections = [
                                    ['key' => 'room',       'label' => 'Room Charges',     'icon' => 'fa-bed',             'type' => 'charge'],
                                    ['key' => 'restaurant', 'label' => 'Restaurant / F&B', 'icon' => 'fa-utensils',        'type' => 'charge'],
                                    ['key' => 'laundry',    'label' => 'Laundry',          'icon' => 'fa-shirt',           'type' => 'charge'],
                                    ['key' => 'tax',        'label' => 'Taxes & Fees',     'icon' => 'fa-landmark',        'type' => 'charge'],
                                    ['key' => 'discounts',  'label' => 'Discounts',        'icon' => 'fa-tag',             'type' => 'charge'],
                                    ['key' => 'other',      'label' => 'Other Charges',    'icon' => 'fa-circle-dot',      'type' => 'charge'],
                                    ['key' => 'payments',   'label' => 'Payments',         'icon' => 'fa-money-bill-wave', 'type' => 'payment'],
                                ];
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 small">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:110px;">Date</th>
                                            <th>Description</th>
                                            <th style="width:200px;">Reference</th>
                                            <th class="text-end" style="width:130px;">Charge (₱)</th>
                                            <th class="text-end" style="width:130px;">Payment (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ledgerSections as $section)
                                            @if($txnsByCategory[$section['key']]->isNotEmpty())
                                                {{-- Section header row --}}
                                                <tr class="table-light">
                                                    <td colspan="5" class="py-1 px-3">
                                                        <span class="fw-semibold text-secondary" style="font-size:0.72rem;letter-spacing:.06em;text-transform:uppercase;">
                                                            <i class="fa-solid {{ $section['icon'] }} me-1"></i>
                                                            {{ $section['label'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                {{-- Transaction rows --}}
                                                @foreach($txnsByCategory[$section['key']] as $txn)
                                                <tr>
                                                    <td class="text-muted ps-3">{{ $txn->transaction_date->format('M d, Y') }}</td>
                                                    <td>{{ $txn->chargeCode?->description ?? "Code #{$txn->charge_code}" }}</td>
                                                    <td class="text-muted">{{ $txn->reference_notes ?: '—' }}</td>
                                                    <td class="text-end">
                                                        @if($txn->charge_amount > 0)
                                                            <span class="fw-semibold text-danger">{{ number_format($txn->charge_amount, 2) }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($txn->credit_amount > 0)
                                                            <span class="fw-semibold text-success">{{ number_format($txn->credit_amount, 2) }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                                {{-- Section subtotal row --}}
                                                <tr style="border-top:1px dashed #dee2e6;">
                                                    <td colspan="3" class="text-end py-1 text-muted ps-3" style="font-size:0.75rem;">
                                                        Subtotal — {{ $section['label'] }}
                                                    </td>
                                                    @if($section['type'] === 'payment')
                                                        <td class="text-end py-1"></td>
                                                        <td class="text-end py-1 fw-semibold text-success">
                                                            {{ number_format($txnsByCategory[$section['key']]->sum('credit_amount'), 2) }}
                                                        </td>
                                                    @else
                                                        <td class="text-end py-1 fw-semibold text-danger">
                                                            {{ number_format($txnsByCategory[$section['key']]->sum('charge_amount'), 2) }}
                                                        </td>
                                                        <td class="text-end py-1"></td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light" style="border-top:2px solid #dee2e6;">
                                        <tr>
                                            <td colspan="3" class="text-end fw-semibold py-2">Total Charges</td>
                                            <td class="text-end fw-bold text-danger py-2">₱{{ number_format($totalCharge, 2) }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end fw-semibold py-2">Total Payments</td>
                                            <td></td>
                                            <td class="text-end fw-bold text-success py-2">₱{{ number_format($totalCredit, 2) }}</td>
                                        </tr>
                                        <tr style="border-top:2px solid #343a40;">
                                            <td colspan="3" class="text-end fw-bold py-2 fs-6">Outstanding Balance</td>
                                            <td class="text-end fw-bold py-2 fs-6 {{ $balance > 0 ? 'text-danger' : ($balance < 0 ? 'text-warning' : 'text-success') }}">₱{{ number_format(abs($balance), 2) }}</td>
                                            <td class="text-end py-2 small text-muted">{{ $balance > 0 ? 'Unpaid' : ($balance < 0 ? 'Overpaid' : 'Settled') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary d-print-none" onclick="printFolio({{ $folio->folio_id }})">
                        <i class="fa-solid fa-print me-1"></i> Print Folio
                    </button>
                    <button type="button" class="btn btn-secondary d-print-none" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    {{-- High-fidelity printable folio view --}}

    <div class="d-none d-print-block print-only-folio" id="print-folio-{{ $folio->folio_id }}" style="font-family: Arial, sans-serif; color: #000000; background: #ffffff; padding: 20px; line-height: 1.4; width: 100%;">
        
        {{-- Header block --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 1px solid #000; padding-bottom: 15px;">
            <tr>
                <!-- Left Logo -->
                <td style="width: 20%; vertical-align: top;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 75px; height: auto; object-fit: contain;">
                </td>
                <!-- Center Hotel Info -->
                <td style="width: 60%; text-align: center; vertical-align: top;">
                    <h3 style="font-size: 18px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Hotel Don Felipe</h3>
                    <div style="font-size: 10px; margin-top: 3px; line-height: 1.3;">
                        Bonifacio Street, Ormoc City<br>
                        Tel. Nos. 255-3580 &bull; Fax No. 561-9620<br>
                        Email: hdfelipe@yahoo.com
                    </div>
                    <h4 style="font-size: 13px; font-weight: bold; margin: 12px 0 0 0; text-transform: uppercase; letter-spacing: 1px;">Guest Folio</h4>
                </td>
                <!-- Right Registration/Folio Numbers -->
                <td style="width: 20%; text-align: right; font-size: 10px; font-weight: bold; line-height: 1.4; vertical-align: top; padding-top: 5px;">
                    <div>REG. NO. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $folio->registration_number ?? '—' }}</div>
                    <div>FOLIO NO. : {{ $folio->folio_number }}</div>
                </td>
            </tr>
        </table>

        {{-- Metadata grid --}}
        <table style="width: 100%; font-size: 11px; margin-bottom: 20px; line-height: 1.5; border-collapse: collapse;">
            <tr>
                <td style="width: 13%; font-weight: bold; vertical-align: top;">DATE</td>
                <td style="width: 37%; vertical-align: top;">: {{ now()->format('m/d/Y') }}</td>
                <td style="width: 13%; font-weight: bold; vertical-align: top;">ROOM</td>
                <td style="width: 37%; vertical-align: top;" colspan="3">
                    : {{ $booking?->room?->room_number ?? 'N/A' }}<br>
                    &nbsp;&nbsp;<strong>{{ number_format($folio->net_rate ?? ($booking?->room?->base_rate ?? 0), 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">GUEST NAME</td>
                <td style="vertical-align: top;">: {{ strtoupper($folio->guest?->last_name ?? '') }}, {{ strtoupper($folio->guest?->first_name ?? '') }}</td>
                <td style="vertical-align: top;" colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">ADDRESS</td>
                <td style="vertical-align: top;">: {{ strtoupper($folio->guest?->address_line1 ?? '') }} {{ strtoupper($folio->guest?->address_line2 ?? '') }}</td>
                <td style="vertical-align: top;" colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">CHECK-IN</td>
                <td style="vertical-align: top;">: {{ $booking?->arrival_date?->format('m/d/Y') ?? 'N/A' }}</td>
                <td style="font-weight: bold; vertical-align: top;">CHECK-OUT</td>
                <td style="vertical-align: top;">: {{ $booking?->departure_date?->format('m/d/Y') ?? 'N/A' }}</td>
                <td style="font-weight: bold; vertical-align: top; width: 12%;">PERSON/S</td>
                <td style="vertical-align: top; width: 8%;">: {{ $folio->num_pax }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">TIME</td>
                <td style="vertical-align: top;">: {{ $booking?->arrival_time ? \Carbon\Carbon::parse($booking->arrival_time)->format('h:i A') : 'N/A' }}</td>
                <td style="font-weight: bold; vertical-align: top;">TIME</td>
                <td style="vertical-align: top;" colspan="3">: {{ $booking?->departure_time ? \Carbon\Carbon::parse($booking->departure_time)->format('h:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">PAYMENT</td>
                <td style="vertical-align: top;">: {{ strtoupper($folio->payment_method ?? 'NONE') }}</td>
                <td style="font-weight: bold; vertical-align: top;">F/DESK</td>
                <td style="vertical-align: top;">: {{ strtoupper(auth()->user()?->full_name ?? auth()->user()?->username ?? 'SYSTEM') }}</td>
                <td style="font-weight: bold; vertical-align: top;">SYMBOL</td>
                <td style="vertical-align: top;">: </td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">MODE</td>
                <td style="vertical-align: top;">: </td>
                <td style="vertical-align: top;" colspan="2">&nbsp;</td>
                <td style="vertical-align: top;" colspan="2">&nbsp;&nbsp;{{ $folio->symbol }}</td>
            </tr>
        </table>

        {{-- Transactions table --}}
        <table style="width: 100%; font-size: 11px; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold;">
                    <th style="padding: 5px 0; text-align: left; width: 15%;">DATE</th>
                    <th style="padding: 5px 0; text-align: left; width: 45%;">REFERENCE</th>
                    <th style="padding: 5px 0; text-align: right; width: 13%;">CHARGE</th>
                    <th style="padding: 5px 0; text-align: right; width: 13%;">CREDIT</th>
                    <th style="padding: 5px 0; text-align: right; width: 14%;">BALANCE</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $runningBal = 0.00;
                    $printSumRoom = 0.00;
                    $printSumRestaurant = 0.00;
                    $printSumLaundry = 0.00;
                    $printSumTax = 0.00;
                    $printSumDiscounts = 0.00;
                    $printSumOther = 0.00;
                    $printSumPayments = 0.00;
                @endphp
                @foreach($folio->transactions->sortBy('timestamp') as $txn)
                    @php
                        $runningBal += ($txn->charge_amount - $txn->credit_amount);
                        $printCode = (int) $txn->charge_code;
                        $printCat  = $txn->chargeCode?->category ?? 'HOTEL';

                        if ($printCat === 'PAYMENT') {
                            $printSumPayments += $txn->credit_amount;
                        } elseif ($printCode === 100 || $printCode === 103) {
                            $printSumRoom += $txn->charge_amount;
                        } elseif ($printCode === 104 || $printCode === 105) {
                            $printSumLaundry += $txn->charge_amount;
                        } elseif ($printCode === 200) {
                            $printSumRestaurant += $txn->charge_amount;
                        } elseif ($printCat === 'TAX_SERVICE') {
                            $printSumTax += $txn->charge_amount;
                        } elseif ($printCode === 201) {
                            $printSumDiscounts += $txn->charge_amount;
                        } else {
                            $printSumOther += $txn->charge_amount;
                        }
                    @endphp
                    <tr>
                        <td style="padding: 4px 0;">{{ $txn->transaction_date->format('m/d/Y') }}</td>
                        <td style="padding: 4px 0;">
                            {{ $txn->chargeCode?->description ?? 'CHARGE' }}
                            @if($txn->reference_notes) — {{ $txn->reference_notes }} @endif
                            @if($txn->charge_number) ({{ $txn->charge_number }}) @endif
                        </td>
                        <td style="padding: 4px 0; text-align: right;">
                            {{ $txn->charge_amount > 0 ? number_format($txn->charge_amount, 2) : '' }}
                        </td>
                        <td style="padding: 4px 0; text-align: right;">
                            {{ $txn->credit_amount > 0 ? number_format($txn->credit_amount, 2) : '' }}
                        </td>
                        <td style="padding: 4px 0; text-align: right;">{{ number_format($runningBal, 2) }}</td>
                    </tr>
                @endforeach
                {{-- Total balance row --}}
                <tr style="border-top: 1px solid #000; border-bottom: 3px double #000; font-weight: bold;">
                    <td colspan="4" style="padding: 8px 0;">Total Balance - ₱</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($runningBal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Nothing follows centered --}}
        <div style="text-align: center; font-size: 10px; font-weight: bold; font-style: italic; margin-bottom: 25px;">
            *** Nothing follows ***
        </div>

        {{-- Summary & Remarks --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-start; font-size: 11px;">
            <!-- Left Remarks -->
            <div style="width: 50%;">
                <div style="font-weight: bold; margin-bottom: 5px;">Remarks :</div>
                <div style="border-bottom: 1px dashed #ccc; height: 40px; width: 90%;"></div>
            </div>
            <!-- Right Summary -->
            <div style="width: 45%;">
                <div style="font-weight: bold; font-style: italic; margin-bottom: 8px;">SUMMARY :</div>
                <table style="width: 100%; border-collapse: collapse; line-height: 1.5;">
                    @if($printSumRoom > 0)
                        <tr>
                            <td>ROOM CHARGES</td>
                            <td style="text-align: right;">{{ number_format($printSumRoom, 2) }}</td>
                        </tr>
                    @endif
                    @if($printSumRestaurant > 0)
                        <tr>
                            <td>RESTAURANT / FOOD &amp; BEVERAGE</td>
                            <td style="text-align: right;">{{ number_format($printSumRestaurant, 2) }}</td>
                        </tr>
                    @endif
                    @if($printSumLaundry > 0)
                        <tr>
                            <td>LAUNDRY SERVICE &amp; PRESSING</td>
                            <td style="text-align: right;">{{ number_format($printSumLaundry, 2) }}</td>
                        </tr>
                    @endif
                    @if($printSumTax > 0)
                        <tr>
                            <td>TAXES &amp; SERVICE CHARGES</td>
                            <td style="text-align: right;">{{ number_format($printSumTax, 2) }}</td>
                        </tr>
                    @endif
                    @if($printSumDiscounts > 0)
                        <tr>
                            <td>DISCOUNTS / COMPLIMENTARY</td>
                            <td style="text-align: right;">{{ number_format($printSumDiscounts, 2) }}</td>
                        </tr>
                    @endif
                    @if($printSumOther > 0)
                        <tr>
                            <td>OTHER CHARGES</td>
                            <td style="text-align: right;">{{ number_format($printSumOther, 2) }}</td>
                        </tr>
                    @endif
                    @if($printSumPayments > 0)
                        <tr style="border-top: 1px solid #999;">
                            <td>TOTAL PAYMENTS</td>
                            <td style="text-align: right;">({{ number_format($printSumPayments, 2) }})</td>
                        </tr>
                    @endif
                    <tr style="border-top: 1px solid #000; border-bottom: 3px double #000; font-weight: bold;">
                        <td style="padding: 5px 0;">OUTSTANDING BALANCE</td>
                        <td style="padding: 5px 0; text-align: right;">{{ number_format($runningBal, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
@endforeach

{{-- Printable Folio List View --}}
<div class="d-none" id="print-folio-list" style="font-family: Arial, sans-serif; color: #000; background: #fff; padding: 20px;">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 12px;">
        <img src="{{ asset('images/logo.png') }}" alt="Hotel Don Felipe" style="width: 70px; height: 70px; object-fit: contain; margin-right: 16px;">
        <div style="text-align: center;">
            <div style="font-size: 18px; font-weight: bold; letter-spacing: 1px;">HOTEL DON FELIPE</div>
            <div style="font-size: 11px; color: #444;">Bonifacio Street, Ormoc City</div>
            <div style="font-size: 11px; color: #444;">Tel. 255-3580 | Fax. 561-9620 | hdfelipe@yahoo.com</div>
            <div style="font-size: 14px; font-weight: bold; margin-top: 6px; letter-spacing: 1px;">GUEST FOLIO LIST</div>
        </div>
    </div>

    {{-- Report Meta --}}
    <div style="font-size: 12px; margin-bottom: 16px; line-height: 1.8;">
        <div><strong>Report Date:</strong> {{ now()->format('m/d/Y h:i A') }}</div>
        @if($search)
            <div><strong>Search:</strong> "{{ $search }}"</div>
        @endif
        @if($folioType !== 'ALL')
            <div><strong>Folio Type:</strong> {{ $folioType }}</div>
        @endif
        @if($statusFilter !== 'ALL')
            <div><strong>Status:</strong> {{ $statusFilter }}</div>
        @endif
        <div><strong>Total Folios:</strong> {{ $folios->total() }}</div>
    </div>

    {{-- Folio Table --}}
    <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 16px;">
        <thead>
            <tr>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: left;">Folio No.</th>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: left;">Guest Name</th>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: left;">Room</th>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: left;">Type</th>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: left;">Arrival</th>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: left;">Departure</th>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: right;">Base Rate</th>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: right;">Net Rate</th>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: center;">Payment</th>
                <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px 0; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($folios as $folio)
                @php
                    $printBooking = $folio->bookings->sortByDesc('booking_id')->first();
                    $printRoom = $printBooking?->room;
                @endphp
                <tr>
                    <td style="padding: 6px 0; font-weight: bold; border-bottom: 1px dashed #ccc;">{{ $folio->folio_number }}</td>
                    <td style="padding: 6px 0; border-bottom: 1px dashed #ccc;">
                        @if($folio->guest)
                            {{ $folio->guest->last_name }}, {{ $folio->guest->first_name }}
                        @else
                            —
                        @endif
                    </td>
                    <td style="padding: 6px 0; border-bottom: 1px dashed #ccc;">
                        @if($printRoom)
                            {{ $printRoom->room_number }} ({{ $printRoom->room_type }})
                        @else
                            —
                        @endif
                    </td>
                    <td style="padding: 6px 0; text-align: left; border-bottom: 1px dashed #ccc;">{{ $folio->folio_type }}</td>
                    <td style="padding: 6px 0; border-bottom: 1px dashed #ccc;">{{ $printBooking?->arrival_date?->format('m/d/Y') ?? '—' }}</td>
                    <td style="padding: 6px 0; border-bottom: 1px dashed #ccc;">{{ $printBooking?->departure_date?->format('m/d/Y') ?? '—' }}</td>
                    <td style="padding: 6px 0; text-align: right; border-bottom: 1px dashed #ccc;">
                        @if($printRoom) {{ number_format($printRoom->base_rate, 2) }} @else — @endif
                    </td>
                    <td style="padding: 6px 0; text-align: right; border-bottom: 1px dashed #ccc;">
                        @if($folio->net_rate !== null) {{ number_format($folio->net_rate, 2) }} @else — @endif
                    </td>
                    <td style="padding: 6px 0; text-align: center; border-bottom: 1px dashed #ccc;">{{ $folio->payment_method ?? '—' }}</td>
                    <td style="padding: 6px 0; text-align: center; border-bottom: 1px dashed #ccc;">{{ $folio->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="padding: 10px; text-align: center; border-bottom: 1px solid #000;">No folios found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="text-align: center; font-size: 11px; font-style: italic; margin: 12px 0; color: #555;">
        *** Nothing follows ***
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 50px; font-size: 12px;">
        <div style="text-align: center;">
            <div style="border-top: 1px solid #000; width: 200px; padding-top: 4px;">Generated By</div>
        </div>
        <div style="text-align: center;">
            <div style="border-top: 1px solid #000; width: 200px; padding-top: 4px;">Verified By</div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .sidebar, .navbar, header, footer, .modal-backdrop, .modal, .d-print-none, .main-content > .card, .container-fluid, #filterForm {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        /* Hide all print folios by default */
        .print-only-folio {
            display: none !important;
        }
        /* Show only the active print folio */
        .print-only-folio.active-print-folio {
            display: block !important;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            margin: 0 !important;
            padding: 10px !important;
            background: #ffffff !important;
            color: #000000 !important;
        }
        /* Folio list print mode */
        #print-folio-list {
            display: none !important;
        }
        #print-folio-list.active-print-list {
            display: block !important;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            margin: 0 !important;
            padding: 10px !important;
            background: #ffffff !important;
            color: #000000 !important;
        }
    }
    
    /* Custom styling for charge account select search */
    .charge-account-select {
        position: relative;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    (function() {
        window.printFolio = function(folioId) {
            // Remove active class from all print containers
            document.querySelectorAll('.print-only-folio').forEach(function(el) {
                el.classList.remove('active-print-folio');
            });
            // Ensure list print mode is off
            var listEl = document.getElementById('print-folio-list');
            if (listEl) listEl.classList.remove('active-print-list');
            
            // Add active class to selected print container
            var printContainer = document.getElementById('print-folio-' + folioId);
            if (printContainer) {
                printContainer.classList.add('active-print-folio');
                window.print();
            }
        };

        window.printFolioList = function() {
            // Remove any active folio print
            document.querySelectorAll('.print-only-folio').forEach(function(el) {
                el.classList.remove('active-print-folio');
            });
            // Activate list print mode
            var listEl = document.getElementById('print-folio-list');
            if (listEl) {
                listEl.classList.add('active-print-list');
                window.print();
                // Clean up after print dialog closes
                setTimeout(function() {
                    listEl.classList.remove('active-print-list');
                }, 500);
            }
        };

        // Keep the active folio modal open after page submits/reloads
        var openModalId = sessionStorage.getItem('openModalId');
        if (openModalId) {
            var modalEl = document.getElementById(openModalId);
            if (modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
            sessionStorage.removeItem('openModalId');
        }

        document.querySelectorAll('.modal form').forEach(function(form) {
            form.addEventListener('submit', function() {
                var modal = form.closest('.modal');
                if (modal && modal.id) {
                    sessionStorage.setItem('openModalId', modal.id);
                }
            });
        });

        // Initialize Select2 for charge account selects
        document.addEventListener('shown.bs.modal', function(e) {
            // When modal is shown, initialize Select2 for any charge account selects in it
            var chargeSelects = e.target.querySelectorAll('.charge-account-select');
            chargeSelects.forEach(function(select) {
                if (!$(select).data('select2')) {
                    $(select).select2({
                        theme: "bootstrap-5",
                        placeholder: "Search or select account...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $(select).closest('.modal')
                    });
                }
            });
        });

        // SweetAlert2 helpers — Module 6
        window.swalConfirmCheckIn = function(btn, guestName, roomNumber) {
            Swal.fire({
                icon: 'question',
                title: 'Confirm Check-In',
                html: 'Check in <strong>' + (guestName || 'guest') + '</strong> to Room <strong>' + (roomNumber || '—') + '</strong>?',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-door-open me-1"></i> Check In',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0d6efd',
                reverseButtons: true,
            }).then(function(result) {
                if (result.isConfirmed) {
                    var form = btn.closest('form');
                    if (form) {
                        sessionStorage.setItem('openModalId', form.closest('.modal').id);
                        form.submit();
                    }
                }
            });
        };

        window.swalConfirmTransfer = function(btn) {
            var form = btn.closest('form');
            var select = form ? form.querySelector('select[name="new_room_id"]') : null;
            var selectedText = select && select.selectedIndex > 0
                ? select.options[select.selectedIndex].text.trim()
                : null;

            if (!selectedText) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Room Selected',
                    text: 'Please select a room before transferring.',
                    confirmButtonColor: '#ffc107',
                });
                return;
            }

            Swal.fire({
                icon: 'question',
                title: 'Confirm Room Transfer',
                html: 'Transfer guest to <strong>' + selectedText + '</strong>?',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-right-left me-1"></i> Transfer',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0d6efd',
                reverseButtons: true,
            }).then(function(result) {
                if (result.isConfirmed) {
                    if (form) {
                        sessionStorage.setItem('openModalId', form.closest('.modal').id);
                        form.submit();
                    }
                }
            });
        };

    })();
</script>
@endpush