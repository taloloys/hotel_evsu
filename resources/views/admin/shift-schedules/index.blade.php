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
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Total Schedules</div>
                    <h4 class="mb-0 fw-bold">{{ $schedules->count() }}</h4>
                </div>
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-calendar-days fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Active Shifts</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $schedules->where('status', 'ACTIVE')->count() }}</h4>
                </div>
                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-business-time fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Scheduled (Upcoming)</div>
                    <h4 class="mb-0 fw-bold text-warning">{{ $schedules->where('status', 'SCHEDULED')->count() }}</h4>
                </div>
                <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-clock fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Completed</div>
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

    <!-- HEADER -->
    <div class="card-header bg-white border-0 pb-2 pt-3">

        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">

            <!-- TITLE -->
            <div>
                <h5 class="fw-bold mb-0">Shift Schedules</h5>
                <small class="text-muted">Create, edit and monitor employee shift schedules</small>
            </div>

            <!-- ADD BUTTON (FIXED: OUTSIDE FORM) -->
            <button class="btn btn-primary d-flex align-items-center gap-2 px-3"
                    style="height: 38px; border-radius: 6px;"
                    data-bs-toggle="modal"
                    data-bs-target="#addScheduleModal">

                <i class="fa-solid fa-plus"></i>
                <span>Schedule Shift</span>
            </button>

        </div>

        <!-- FILTERS ROW -->
        <div class="mt-3 pt-2">

            <form method="GET"
                  action="{{ route('admin.shift-schedules') }}"
                  class="d-flex align-items-center gap-2 flex-wrap justify-content-end m-0">

                <!-- DATE FROM -->
                <div class="input-group"
                     style="width: 170px; border: 1px solid #ced4da; border-radius: 6px; overflow: hidden; height: 38px;">

                    <span class="input-group-text bg-white border-0 text-muted small">From</span>

                    <input type="date"
                           name="date_from"
                           value="{{ $filters['date_from'] ?? '' }}"
                           class="form-control border-0 shadow-none"
                           onchange="this.form.submit()">
                </div>

                <!-- DATE TO -->
                <div class="input-group"
                     style="width: 170px; border: 1px solid #ced4da; border-radius: 6px; overflow: hidden; height: 38px;">

                    <span class="input-group-text bg-white border-0 text-muted small">To</span>

                    <input type="date"
                           name="date_to"
                           value="{{ $filters['date_to'] ?? '' }}"
                           class="form-control border-0 shadow-none"
                           onchange="this.form.submit()">
                </div>

                <!-- EMPLOYEE -->
                <select name="user_id"
                        class="form-select"
                        style="width: 180px; height: 38px; border-radius: 6px;"
                        onchange="this.form.submit()">

                    <option value="">All Employees</option>
                    @foreach($users as $u)
                        <option value="{{ $u->user_id }}"
                            {{ ($filters['user_id'] ?? '') == $u->user_id ? 'selected' : '' }}>
                            {{ $u->full_name }}
                        </option>
                    @endforeach

                </select>

                <!-- STATUS -->
                <select name="status"
                        class="form-select"
                        style="width: 160px; height: 38px; border-radius: 6px;"
                        onchange="this.form.submit()">

                    <option value="">All Statuses</option>
                    <option value="SCHEDULED" {{ ($filters['status'] ?? '') === 'SCHEDULED' ? 'selected' : '' }}>Scheduled</option>
                    <option value="ACTIVE" {{ ($filters['status'] ?? '') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                    <option value="COMPLETED" {{ ($filters['status'] ?? '') === 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                    <option value="MISSED" {{ ($filters['status'] ?? '') === 'MISSED' ? 'selected' : '' }}>Missed</option>

                </select>

                <!-- RESET (FIXED CONDITION) -->
                @if(request()->hasAny(['date_from','date_to','user_id','status']))
                    <a href="{{ route('admin.shift-schedules') }}"
                       class="btn btn-outline-danger d-flex align-items-center gap-2"
                       style="height: 38px; border-radius: 6px;">

                        <i class="fa-solid fa-rotate"></i>
                        <span>Reset</span>

                    </a>
                @endif

            </form>

        </div>

    </div>

    <!-- TABLE BODY (UNCHANGED) -->
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
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold"
                                         style="width:40px;height:40px;">
                                        {{ substr($sched->user->full_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $sched->user->full_name }}</div>
                                        <small class="text-muted">{{ $sched->user->role?->role_name ?? 'Staff' }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $sched->shift_name }}</td>
                            <td>{{ $sched->shift_date->format('M d, Y') }}</td>

                            <td>
                                {{ Carbon\Carbon::parse($sched->scheduled_start_time)->format('g:i A') }}
                                -
                                {{ Carbon\Carbon::parse($sched->scheduled_end_time)->format('g:i A') }}
                            </td>

                            <td>
                                @if($actualShift)
                                    {{ $actualShift->start_time->format('g:i A') }} -
                                    {{ $actualShift->end_time ? $actualShift->end_time->format('g:i A') : 'Active' }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge {{ $statusBadge }}">{{ $sched->status }}</span>
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.shift-schedules.report', $sched) }}"
                                       class="btn btn-outline-primary btn-sm px-2">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </a>

                                    @if($sched->status === 'SCHEDULED')
                                        <button class="btn btn-outline-warning btn-sm px-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editScheduleModal">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <form action="{{ route('admin.shift-schedules.delete', $sched) }}"
                                              method="POST"
                                              class="d-inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm px-2">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
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
     ADD SCHEDULE MODAL (UI IMPROVED ONLY)
     ========================================================= -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">

            <!-- HEADER -->
            <div class="modal-header px-4 py-3">
                <h5 class="modal-title fw-bold" id="addScheduleModalLabel">
                    <i class="fa-solid fa-plus me-2 text-primary"></i>
                    Schedule Staff Shift
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.shift-schedules.store') }}">
                @csrf

                <!-- BODY -->
                <div class="modal-body px-4 py-3">

                    <!-- EMPLOYEE -->
                    <div class="mb-3">
                        <label for="add_user_id" class="form-label fw-semibold">Employee / Cashier</label>
                        <select id="add_user_id" name="user_id" class="form-select form-select-lg" required>
                            <option value="">Select Employee</option>
                            @foreach($users as $u)
                                <option value="{{ $u->user_id }}">
                                    {{ $u->full_name }} ({{ $u->role?->role_name ?? 'No Role' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SHIFT NAME -->
                    <div class="mb-3">
                        <label for="add_shift_name" class="form-label fw-semibold">Shift Name / Description</label>
                        <input type="text"
                               id="add_shift_name"
                               name="shift_name"
                               class="form-control form-control-lg"
                               placeholder="e.g. Morning Frontdesk"
                               required>
                    </div>

                    <!-- DATE -->
                    <div class="mb-3">
                        <label for="add_shift_date" class="form-label fw-semibold">Shift Date</label>
                        <input type="date"
                               id="add_shift_date"
                               name="shift_date"
                               class="form-control form-control-lg"
                               required>
                    </div>

                    <!-- TIME -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="add_start_time" class="form-label fw-semibold">Start Time</label>
                            <input type="time"
                                   id="add_start_time"
                                   name="scheduled_start_time"
                                   class="form-control form-control-lg"
                                   required>
                        </div>

                        <div class="col-6">
                            <label for="add_end_time" class="form-label fw-semibold">End Time</label>
                            <input type="time"
                                   id="add_end_time"
                                   name="scheduled_end_time"
                                   class="form-control form-control-lg"
                                   required>
                        </div>
                    </div>

                    <!-- NOTES -->
                    <div class="mb-2">
                        <label for="add_notes" class="form-label fw-semibold">Notes (Optional)</label>
                        <textarea id="add_notes"
                                  name="notes"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Shift duties, drawer keys, etc."></textarea>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary px-4">
                        Save Schedule
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- =========================================================
     EDIT SCHEDULE MODAL (UI IMPROVED ONLY)
     ========================================================= -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">

            <!-- HEADER -->
            <div class="modal-header px-4 py-3">
                <h5 class="modal-title fw-bold" id="editScheduleModalLabel">
                    <i class="fa-solid fa-pen me-2 text-warning"></i>
                    Edit Shift Schedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" id="editScheduleForm" action="">
                @csrf
                @method('PATCH')

                <!-- BODY -->
                <div class="modal-body px-4 py-3">

                    <!-- EMPLOYEE -->
                    <div class="mb-3">
                        <label for="edit_user_id" class="form-label fw-semibold">
                            Employee / Cashier (Reassign)
                        </label>

                        <select id="edit_user_id"
                                name="user_id"
                                class="form-select form-select-lg"
                                required>

                            @foreach($users as $u)
                                <option value="{{ $u->user_id }}">
                                    {{ $u->full_name }} ({{ $u->role?->role_name ?? 'No Role' }})
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- SHIFT NAME -->
                    <div class="mb-3">
                        <label for="edit_shift_name" class="form-label fw-semibold">
                            Shift Name / Description
                        </label>

                        <input type="text"
                               id="edit_shift_name"
                               name="shift_name"
                               class="form-control form-control-lg"
                               required>
                    </div>

                    <!-- DATE -->
                    <div class="mb-3">
                        <label for="edit_shift_date" class="form-label fw-semibold">
                            Shift Date
                        </label>

                        <input type="date"
                               id="edit_shift_date"
                               name="shift_date"
                               class="form-control form-control-lg"
                               required>
                    </div>

                    <!-- TIME -->
                    <div class="row g-3 mb-3">

                        <div class="col-6">
                            <label for="edit_start_time" class="form-label fw-semibold">
                                Start Time
                            </label>

                            <input type="time"
                                   id="edit_start_time"
                                   name="scheduled_start_time"
                                   class="form-control form-control-lg"
                                   required>
                        </div>

                        <div class="col-6">
                            <label for="edit_end_time" class="form-label fw-semibold">
                                End Time
                            </label>

                            <input type="time"
                                   id="edit_end_time"
                                   name="scheduled_end_time"
                                   class="form-control form-control-lg"
                                   required>
                        </div>

                    </div>

                    <!-- NOTES -->
                    <div class="mb-2">
                        <label for="edit_notes" class="form-label fw-semibold">
                            Notes (Optional)
                        </label>

                        <textarea id="edit_notes"
                                  name="notes"
                                  class="form-control"
                                  rows="3"></textarea>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 py-3">

                    <button type="button"
                            class="btn btn-light px-4"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-warning text-white px-4">
                        Save Changes
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
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
    })();
</script>
@endpush
