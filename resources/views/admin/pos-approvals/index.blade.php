@extends('layouts.app')

@section('title', 'POS Approvals')
@section('pageTitle', 'POS Approval Queue')
@section('pageSubtitle', 'Authorize or reject coffee shop voids, refunds, and cancellations')

@section('content')
<div class="container-fluid px-0">
    <!-- Display generic errors/successes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- PENDING REQUESTS -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clock text-warning me-2"></i>Pending Authorizations</h5>
            <span class="badge bg-warning text-dark" id="pending-count-badge">{{ $pendingRequests->count() }} pending</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="pending-table">
                <thead class="table-light">
                    <tr>
                        <th>Request Type</th>
                        <th>Cashier / User</th>
                        <th>Details</th>
                        <th>Total Amount</th>
                        <th>Reason for Request</th>
                        <th>Requested At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="pending-tbody">
                    @forelse($pendingRequests as $req)
                        @php
                            $badgeColor = match($req->request_type) {
                                'refund' => 'danger',
                                'cancel_tab' => 'warning text-dark',
                                'cancel_order' => 'secondary',
                                default => 'primary'
                            };
                            $typeLabel = match($req->request_type) {
                                'refund' => 'REFUND ORDER',
                                'cancel_tab' => 'VOID TAB',
                                'cancel_order' => 'CANCEL ORDER',
                                default => strtoupper($req->request_type)
                            };
                        @endphp
                        <tr id="req-row-{{ $req->request_id }}">
                            <td>
                                <span class="badge bg-{{ $badgeColor }}">{{ $typeLabel }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $req->requestedBy?->full_name ?? 'Unknown' }}</div>
                                <small class="text-muted">{{ $req->requestedBy?->role?->role_name ?? 'Cashier' }}</small>
                            </td>
                            <td>
                                @if($req->order)
                                    <div><strong>Order #:</strong> {{ $req->order->order_number }}</div>
                                    <small class="text-muted">Customer: {{ $req->order->customer_name }} ({{ $req->order->room_number ?? 'Walk-in' }})</small>
                                @elseif($req->tab)
                                    <div><strong>Tab Name:</strong> {{ $req->tab->tab_name }}</div>
                                    <small class="text-muted">Type: {{ strtoupper($req->tab->tab_type) }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                  @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark">
                                    ₱{{ number_format($req->order?->total ?? $req->tab?->total ?? 0.00, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-dark fst-italic">"{{ $req->reason ?? 'No reason provided' }}"</span>
                            </td>
                            <td>
                                <div>{{ $req->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $req->created_at->format('h:i A') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2 px-3">
                                    <form action="{{ route('admin.pos-approvals.approve', $req->request_id) }}" method="POST" class="approval-form" data-type="approve" data-id="{{ $req->request_id }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success px-3"><i class="fa-solid fa-check me-1"></i>Approve</button>
                                    </form>
                                    <form action="{{ route('admin.pos-approvals.reject', $req->request_id) }}" method="POST" class="approval-form" data-type="reject" data-id="{{ $req->request_id }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger px-3"><i class="fa-solid fa-xmark me-1"></i>Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-circle-check fs-2 text-success mb-2 opacity-50"></i>
                                <div class="small">No pending authorization requests. Everything is clear!</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- RESOLVED HISTORY -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-history text-muted me-2"></i>Authorization Log</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Request Type</th>
                        <th>Details</th>
                        <th>Requested By</th>
                        <th>Resolved By</th>
                        <th>Reason</th>
                        <th>Resolved At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resolvedRequests as $req)
                        @php
                            $badgeColor = match($req->request_type) {
                                'refund' => 'danger',
                                'cancel_tab' => 'warning text-dark',
                                'cancel_order' => 'secondary',
                                default => 'primary'
                            };
                            $typeLabel = match($req->request_type) {
                                'refund' => 'REFUND',
                                'cancel_tab' => 'VOID TAB',
                                'cancel_order' => 'CANCEL ORDER',
                                default => strtoupper($req->request_type)
                            };
                        @endphp
                        <tr>
                            <td>
                                <span class="badge bg-{{ $badgeColor }}">{{ $typeLabel }}</span>
                            </td>
                            <td>
                                @if($req->order)
                                    <div><strong>Order #:</strong> {{ $req->order->order_number }}</div>
                                    <small class="text-muted">₱{{ number_format($req->order->total, 2) }}</small>
                                @elseif($req->tab)
                                    <div><strong>Tab Name:</strong> {{ $req->tab->tab_name }}</div>
                                    <small class="text-muted">₱{{ number_format($req->tab->total, 2) }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $req->requestedBy?->full_name ?? '—' }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-primary">{{ $req->resolvedBy?->full_name ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="text-muted fst-italic small">"{{ $req->reason }}"</span>
                            </td>
                            <td>
                                <div class="small">{{ $req->resolved_at ? $req->resolved_at->format('M d, Y h:i A') : '—' }}</div>
                            </td>
                            <td>
                                @if($req->status === 'approved')
                                    <span class="badge bg-success"><i class="fa-solid fa-check-circle me-1"></i>Approved</span>
                                @else
                                    <span class="badge bg-danger"><i class="fa-solid fa-times-circle me-1"></i>Rejected</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">No resolved logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($resolvedRequests->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $resolvedRequests->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" id="confirm-modal-header">
                <h5 class="modal-title" id="confirmActionModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p id="confirm-modal-message">Are you sure you want to perform this action?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm text-white" id="confirm-modal-submit-btn">Proceed</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Confirmation modal setup
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
    const confirmSubmitBtn = document.getElementById('confirm-modal-submit-btn');
    const confirmHeader = document.getElementById('confirm-modal-header');
    const confirmTitle = document.getElementById('confirmActionModalLabel');
    const confirmMessage = document.getElementById('confirm-modal-message');

    let formToSubmit = null;

    // Delegate form submission
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.classList.contains('approval-form')) {
            e.preventDefault();
            formToSubmit = e.target;
            const actionType = formToSubmit.dataset.type; // 'approve' or 'reject'
            
            if (actionType === 'approve') {
                confirmHeader.className = 'modal-header bg-success text-white';
                confirmSubmitBtn.className = 'btn btn-success btn-sm text-white';
                confirmTitle.textContent = 'Approve POS Request';
                confirmMessage.textContent = 'Are you sure you want to approve and execute this authorization request?';
            } else {
                confirmHeader.className = 'modal-header bg-danger text-white';
                confirmSubmitBtn.className = 'btn btn-danger btn-sm text-white';
                confirmTitle.textContent = 'Reject POS Request';
                confirmMessage.textContent = 'Are you sure you want to reject this authorization request?';
            }
            
            confirmModal.show();
        }
    });

    confirmSubmitBtn.addEventListener('click', async function() {
        if (!formToSubmit) return;
        confirmModal.hide();
        
        const form = formToSubmit;
        const rowId = 'req-row-' + form.dataset.id;
        const row = document.getElementById(rowId);
        
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                }
            });
            
            const data = await res.json();
            
            if (res.ok) {
                showSuccessAlert(data.message);
                
                if (row) {
                    row.style.transition = 'opacity 0.5s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        updatePendingCount();
                    }, 500);
                } else {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                showErrorAlert(data.message || 'An error occurred.');
            }
        } catch (err) {
            console.error('Submit error:', err);
            showErrorAlert('An error occurred processing request.');
        }
    });

    function showSuccessAlert(msg) {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" id="ajax-alert">
                <i class="fa-solid fa-circle-check me-2"></i>${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        const container = document.querySelector('.container-fluid');
        document.getElementById('ajax-alert')?.remove();
        container.insertAdjacentHTML('afterbegin', alertHtml);
        setTimeout(() => document.getElementById('ajax-alert')?.remove(), 4000);
    }

    function showErrorAlert(msg) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" id="ajax-alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        const container = document.querySelector('.container-fluid');
        document.getElementById('ajax-alert')?.remove();
        container.insertAdjacentHTML('afterbegin', alertHtml);
    }

    function updatePendingCount() {
        const tbody = document.getElementById('pending-tbody');
        const rows = tbody.querySelectorAll('tr[id^="req-row-"]');
        const count = rows.length;
        
        const countBadge = document.getElementById('pending-count-badge');
        if (countBadge) {
            countBadge.textContent = `${count} pending`;
        }
        
        if (count === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-circle-check fs-2 text-success mb-2 opacity-50"></i>
                        <div class="small">No pending authorization requests. Everything is clear!</div>
                    </td>
                </tr>
            `;
        }
    }

    async function pollPendingRequests() {
        try {
            const res = await fetch(location.href);
            if (!res.ok) return;
            const html = await res.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newTbody = doc.getElementById('pending-tbody');
            if (newTbody) {
                const currentTbody = document.getElementById('pending-tbody');
                const currentRowsMap = new Map();
                currentTbody.querySelectorAll('tr[id^="req-row-"]').forEach(row => {
                    currentRowsMap.set(row.id, row);
                });
                
                const newRows = newTbody.querySelectorAll('tr[id^="req-row-"]');
                let hasChanges = false;
                const keptIds = new Set();
                
                newRows.forEach(newRow => {
                    keptIds.add(newRow.id);
                    if (!currentRowsMap.has(newRow.id)) {
                        hasChanges = true;
                        newRow.style.opacity = '0';
                        newRow.style.transition = 'opacity 0.5s ease';
                        currentTbody.insertBefore(newRow, currentTbody.firstChild);
                        setTimeout(() => newRow.style.opacity = '1', 50);
                    }
                });
                
                currentRowsMap.forEach((row, id) => {
                    if (!keptIds.has(id)) {
                        hasChanges = true;
                        row.style.transition = 'opacity 0.5s ease';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 500);
                    }
                });
                
                if (hasChanges || newRows.length === 0) {
                    setTimeout(() => {
                        updatePendingCount();
                    }, 600);
                }
            }
        } catch (err) {
            console.error('Polling error:', err);
        }
    }

    setInterval(pollPendingRequests, 5000);
</script>
@endpush
@endsection
