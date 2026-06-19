@extends('layouts.app')

@section('title', 'Shift Scheduling')
@section('pageTitle', 'Shift Scheduling')
@section('pageSubtitle', 'Assign and manage staff work shifts')

@section('content')

{{-- TOAST CONTAINER --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    @if(session('success'))
        <div id="successToast" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first() }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif
</div>

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Schedules</div>
                    <h4 class="mb-0 fw-bold">{{ $schedules->count() }}</h4>
                </div>
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-calendar-days fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Active Shifts</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $schedules->where('status', 'ACTIVE')->count() }}</h4>
                </div>
                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-business-time fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Scheduled (Upcoming)</div>
                    <h4 class="mb-0 fw-bold text-warning">{{ $schedules->where('status', 'SCHEDULED')->count() }}</h4>
                </div>
                <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-clock fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Completed</div>
                    <h4 class="mb-0 fw-bold text-secondary">{{ $schedules->where('status', 'COMPLETED')->count() }}</h4>
                </div>
                <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-circle-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCHEDULES TABLE CARD -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center py-3 gap-2">
        <div>
            <h5 class="fw-bold mb-0">Shift Schedules</h5>
            <small class="text-muted">Create, edit and monitor employee shift schedules</small>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <!-- DATE FILTER FROM CONTROLLER / FORM -->
            <form method="GET" action="{{ route('admin.shift-schedules') }}" class="d-flex gap-2 flex-wrap m-0 align-items-center">
                <div class="input-group input-group-sm" style="width: 150px;">
                    <span class="input-group-text bg-light text-muted small">From</span>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
                </div>
                <div class="input-group input-group-sm" style="width: 150px;">
                    <span class="input-group-text bg-light text-muted small">To</span>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
                </div>

                <select name="user_id" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">All Employees</option>
                    @foreach($users as $u)
                        <option value="{{ $u->user_id }}" {{ (isset($filters['user_id']) && $filters['user_id'] == $u->user_id) ? 'selected' : '' }}>
                            {{ $u->full_name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="form-select form-select-sm" style="width: 130px;">
                    <option value="">All Statuses</option>
                    <option value="SCHEDULED" {{ (isset($filters['status']) && $filters['status'] === 'SCHEDULED') ? 'selected' : '' }}>Scheduled</option>
                    <option value="ACTIVE" {{ (isset($filters['status']) && $filters['status'] === 'ACTIVE') ? 'selected' : '' }}>Active</option>
                    <option value="COMPLETED" {{ (isset($filters['status']) && $filters['status'] === 'COMPLETED') ? 'selected' : '' }}>Completed</option>
                    <option value="MISSED" {{ (isset($filters['status']) && $filters['status'] === 'MISSED') ? 'selected' : '' }}>Missed</option>
                </select>

                <button type="submit" class="btn btn-outline-primary btn-sm px-2">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(count($filters) > 0)
                    <a href="{{ route('admin.shift-schedules') }}" class="btn btn-outline-secondary btn-sm px-2">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                    </a>
                @endif
            </form>

            <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                <i class="fa-solid fa-plus me-1"></i>
                Schedule Shift
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Employee</th>
                        <th>Shift Details</th>
                        <th>Date</th>
                        <th>Scheduled Time</th>
                        <th>Actual Hours</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 160px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $sched)
                        @php
                            $statusBadge = match($sched->status) {
                                'SCHEDULED' => 'bg-warning text-dark',
                                'ACTIVE' => 'bg-success',
                                'COMPLETED' => 'bg-secondary',
                                'MISSED' => 'bg-danger',
                                default => 'bg-info'
                            };
                            $actualShift = $sched->actualShift;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold" style="width:40px;height:40px;">
                                        {{ substr($sched->user->full_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $sched->user->full_name }}</div>
                                        <small class="text-muted">{{ $sched->user->role?->role_name ?? 'Staff' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-medium text-dark">{{ $sched->shift_name }}</span>
                                @if($sched->notes)
                                    <br><small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">{{ $sched->notes }}</small>
                                @endif
                            </td>
                            <td>{{ $sched->shift_date->format('M d, Y') }}</td>
                            <td>{{ Carbon\Carbon::parse($sched->scheduled_start_time)->format('g:i A') }} - {{ Carbon\Carbon::parse($sched->scheduled_end_time)->format('g:i A') }}</td>
                            <td>
                                @if($actualShift)
                                    <span class="small">
                                        {{ $actualShift->start_time->format('g:i A') }} - 
                                        {{ $actualShift->end_time ? $actualShift->end_time->format('g:i A') : 'Active' }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $statusBadge }}">{{ $sched->status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- REPORT / SALES VIEW -->
                                    <a href="{{ route('admin.shift-schedules.report', $sched) }}" class="btn btn-outline-primary btn-sm px-2" title="View Shift Sales Report">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </a>

                                    @if($sched->status === 'SCHEDULED')
                                        <!-- EDIT BUTTON -->
                                        <button type="button" 
                                                class="btn btn-outline-warning btn-sm px-2"
                                                title="Edit Schedule"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editScheduleModal"
                                                data-schedule-id="{{ $sched->id }}"
                                                data-user-id="{{ $sched->user_id }}"
                                                data-shift-name="{{ $sched->shift_name }}"
                                                data-shift-date="{{ $sched->shift_date->format('Y-m-d') }}"
                                                data-start-time="{{ substr($sched->scheduled_start_time, 0, 5) }}"
                                                data-end-time="{{ substr($sched->scheduled_end_time, 0, 5) }}"
                                                data-notes="{{ $sched->notes }}"
                                                data-update-url="{{ route('admin.shift-schedules.update', $sched) }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <!-- DELETE BUTTON -->
                                        <form action="{{ route('admin.shift-schedules.delete', $sched) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to delete this shift schedule?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm px-2" title="Delete Schedule">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <!-- DISABLED PLACEHOLDERS -->
                                        <button class="btn btn-outline-secondary btn-sm px-2" disabled title="Started shifts cannot be edited">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm px-2" disabled title="Started shifts cannot be deleted">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No shift schedules found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================
     ADD SCHEDULE MODAL
     ========================================================= -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addScheduleModalLabel">
                    <i class="fa-solid fa-plus me-2 text-primary"></i>Schedule Staff Shift
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.shift-schedules.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_user_id" class="form-label fw-semibold">Employee / Cashier</label>
                        <select id="add_user_id" name="user_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($users as $u)
                                <option value="{{ $u->user_id }}">{{ $u->full_name }} ({{ $u->role?->role_name ?? 'No Role' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_shift_name" class="form-label fw-semibold">Shift Name / Description</label>
                        <input type="text" id="add_shift_name" name="shift_name" class="form-control" placeholder="e.g. Morning Frontdesk" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_shift_date" class="form-label fw-semibold">Shift Date</label>
                        <input type="date" id="add_shift_date" name="shift_date" class="form-control" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="add_start_time" class="form-label fw-semibold">Start Time</label>
                            <input type="time" id="add_start_time" name="scheduled_start_time" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label for="add_end_time" class="form-label fw-semibold">End Time</label>
                            <input type="time" id="add_end_time" name="scheduled_end_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="add_notes" class="form-label fw-semibold">Notes (Optional)</label>
                        <textarea id="add_notes" name="notes" class="form-control" placeholder="Shift duties, drawer keys, etc." rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     EDIT SCHEDULE MODAL
     ========================================================= -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editScheduleModalLabel">
                    <i class="fa-solid fa-pen me-2 text-warning"></i>Edit Shift Schedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editScheduleForm" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_user_id" class="form-label fw-semibold">Employee / Cashier (Reassign)</label>
                        <select id="edit_user_id" name="user_id" class="form-select" required>
                            @foreach($users as $u)
                                <option value="{{ $u->user_id }}">{{ $u->full_name }} ({{ $u->role?->role_name ?? 'No Role' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_shift_name" class="form-label fw-semibold">Shift Name / Description</label>
                        <input type="text" id="edit_shift_name" name="shift_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_shift_date" class="form-label fw-semibold">Shift Date</label>
                        <input type="date" id="edit_shift_date" name="shift_date" class="form-control" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="edit_start_time" class="form-label fw-semibold">Start Time</label>
                            <input type="time" id="edit_start_time" name="scheduled_start_time" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label for="edit_end_time" class="form-label fw-semibold">End Time</label>
                            <input type="time" id="edit_end_time" name="scheduled_end_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_notes" class="form-label fw-semibold">Notes (Optional)</label>
                        <textarea id="edit_notes" name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto show toasts
        document.querySelectorAll('.toast').forEach(function (el) {
            new bootstrap.Toast(el).show();
        });

        // Populate Edit Modal
        const editScheduleModal = document.getElementById('editScheduleModal');
        if (editScheduleModal) {
            editScheduleModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('edit_user_id').value = btn.dataset.userId;
                document.getElementById('edit_shift_name').value = btn.dataset.shiftName;
                document.getElementById('edit_shift_date').value = btn.dataset.shiftDate;
                document.getElementById('edit_start_time').value = btn.dataset.startTime;
                document.getElementById('edit_end_time').value = btn.dataset.endTime;
                document.getElementById('edit_notes').value = btn.dataset.notes || '';
                document.getElementById('editScheduleForm').action = btn.dataset.updateUrl;
            });
        }
    });
</script>
@endpush
