@extends('layouts.app')

@section('title', 'Shift Scheduling')
@section('pageTitle', 'Shift Scheduling')
@section('pageSubtitle', 'Assign and manage staff recurring shift rules')

@section('content')



<!-- SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Total Rules</div>
                    <h4 class="mb-0 fw-bold">{{ $schedules->count() }}</h4>
                </div>
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-calendar-days fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Active Rules</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $schedules->where('is_active', true)->count() }}</h4>
                </div>
                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Inactive Rules</div>
                    <h4 class="mb-0 fw-bold text-secondary">{{ $schedules->where('is_active', false)->count() }}</h4>
                </div>
                <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-pause fs-4"></i>
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
                <small class="text-muted">Create, edit and manage recurring staff shift rules</small>
            </div>

            <!-- ADD BUTTON -->
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

                <!-- SEARCH -->
                <div style="width: 250px;">
                    <div class="input-group"
                         style="border: 1px solid #000000; border-radius: 6px; overflow: hidden; height: 38px;">
                        <span class="input-group-text bg-white border-0">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>
                        <input type="text"
                               id="scheduleSearchInput"
                               class="form-control border-0 shadow-none"
                               placeholder="Search shift or employee..."
                               autocomplete="off">
                    </div>
                </div>

                <!-- EMPLOYEE -->
                <select name="user_id"
                        class="form-select"
                        style="width: 180px; height: 38px; border-radius: 6px; border: 1px solid #000000;"
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
                        style="width: 160px; height: 38px; border-radius: 6px; border: 1px solid #000000;"
                        onchange="this.form.submit()">

                    <option value="">All Statuses</option>
                    <option value="ACTIVE" {{ ($filters['status'] ?? '') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                    <option value="INACTIVE" {{ ($filters['status'] ?? '') === 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                </select>

                <!-- RESET -->
                @if(request()->hasAny(['user_id','status']))
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

    <!-- TABLE BODY -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Employee</th>
                        <th>Shift Details</th>
                        <th>Days Active</th>
                        <th>Scheduled Time</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 160px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($schedules as $sched)
                        @php
                            $statusBadge = $sched->is_active ? 'bg-success' : 'bg-secondary';
                            $statusText = $sched->is_active ? 'ACTIVE' : 'INACTIVE';
                            
                            $days = [];
                            if($sched->is_monday) $days[] = 'M';
                            if($sched->is_tuesday) $days[] = 'T';
                            if($sched->is_wednesday) $days[] = 'W';
                            if($sched->is_thursday) $days[] = 'Th';
                            if($sched->is_friday) $days[] = 'F';
                            if($sched->is_saturday) $days[] = 'Sa';
                            if($sched->is_sunday) $days[] = 'Su';
                            $daysStr = implode('-', $days);
                        @endphp

                        <tr data-shift-name="{{ strtolower($sched->shift_name) }}"
                            data-employee-name="{{ strtolower($sched->user->full_name) }}">
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
                            
                            <td>
                                <span class="badge bg-light text-dark border">{{ $daysStr ?: 'None' }}</span>
                            </td>

                            <td>
                                {{ Carbon\Carbon::parse($sched->scheduled_start_time)->format('g:i A') }}
                                -
                                {{ Carbon\Carbon::parse($sched->scheduled_end_time)->format('g:i A') }}
                            </td>

                            <td>
                                <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-outline-warning btn-sm px-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editScheduleModal"
                                            data-id="{{ $sched->id }}"
                                            data-user-id="{{ $sched->user_id }}"
                                            data-shift-name="{{ $sched->shift_name }}"
                                            data-start-time="{{ $sched->scheduled_start_time }}"
                                            data-end-time="{{ $sched->scheduled_end_time }}"
                                            data-is-monday="{{ $sched->is_monday ? '1' : '0' }}"
                                            data-is-tuesday="{{ $sched->is_tuesday ? '1' : '0' }}"
                                            data-is-wednesday="{{ $sched->is_wednesday ? '1' : '0' }}"
                                            data-is-thursday="{{ $sched->is_thursday ? '1' : '0' }}"
                                            data-is-friday="{{ $sched->is_friday ? '1' : '0' }}"
                                            data-is-saturday="{{ $sched->is_saturday ? '1' : '0' }}"
                                            data-is-sunday="{{ $sched->is_sunday ? '1' : '0' }}"
                                            data-is-active="{{ $sched->is_active ? '1' : '0' }}"
                                            data-notes="{{ $sched->notes }}"
                                            data-update-url="{{ route('admin.shift-schedules.update', $sched) }}">
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
                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr id="noResultsRow">
                            <td colspan="6" class="text-center py-4 text-muted">
                                No shift schedules found.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="noFilterResultsRow" style="display:none;">
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-magnifying-glass me-2"></i>No shift schedules match your search.
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>

</div>

<!-- =========================================================
     ADD SCHEDULE MODAL
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

                    <!-- RECURRING DAYS -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recurring Days</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="days[]" value="monday" id="add_day_mon">
                                <label class="form-check-label" for="add_day_mon">Monday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="days[]" value="tuesday" id="add_day_tue">
                                <label class="form-check-label" for="add_day_tue">Tuesday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="days[]" value="wednesday" id="add_day_wed">
                                <label class="form-check-label" for="add_day_wed">Wednesday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="days[]" value="thursday" id="add_day_thu">
                                <label class="form-check-label" for="add_day_thu">Thursday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="days[]" value="friday" id="add_day_fri">
                                <label class="form-check-label" for="add_day_fri">Friday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="days[]" value="saturday" id="add_day_sat">
                                <label class="form-check-label" for="add_day_sat">Saturday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="days[]" value="sunday" id="add_day_sun">
                                <label class="form-check-label" for="add_day_sun">Sunday</label>
                            </div>
                        </div>
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

                    <!-- STATUS & NOTES -->
                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="add_is_active" name="is_active" value="1" checked>
                                <label class="form-check-label fw-semibold" for="add_is_active">Schedule is Active</label>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="add_notes" class="form-label fw-semibold">Notes (Optional)</label>
                            <textarea id="add_notes"
                                      name="notes"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Shift duties, drawer keys, etc."></textarea>
                        </div>
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
     EDIT SCHEDULE MODAL
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

                    <!-- RECURRING DAYS -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recurring Days</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input edit-day-checkbox" type="checkbox" name="days[]" value="monday" id="edit_day_mon">
                                <label class="form-check-label" for="edit_day_mon">Monday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input edit-day-checkbox" type="checkbox" name="days[]" value="tuesday" id="edit_day_tue">
                                <label class="form-check-label" for="edit_day_tue">Tuesday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input edit-day-checkbox" type="checkbox" name="days[]" value="wednesday" id="edit_day_wed">
                                <label class="form-check-label" for="edit_day_wed">Wednesday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input edit-day-checkbox" type="checkbox" name="days[]" value="thursday" id="edit_day_thu">
                                <label class="form-check-label" for="edit_day_thu">Thursday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input edit-day-checkbox" type="checkbox" name="days[]" value="friday" id="edit_day_fri">
                                <label class="form-check-label" for="edit_day_fri">Friday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input edit-day-checkbox" type="checkbox" name="days[]" value="saturday" id="edit_day_sat">
                                <label class="form-check-label" for="edit_day_sat">Saturday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input edit-day-checkbox" type="checkbox" name="days[]" value="sunday" id="edit_day_sun">
                                <label class="form-check-label" for="edit_day_sun">Sunday</label>
                            </div>
                        </div>
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

                    <!-- STATUS & NOTES -->
                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                                <label class="form-check-label fw-semibold" for="edit_is_active">Schedule is Active</label>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="edit_notes" class="form-label fw-semibold">
                                Notes (Optional)
                            </label>

                            <textarea id="edit_notes"
                                      name="notes"
                                      class="form-control"
                                      rows="3"></textarea>
                        </div>
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

        // Populate Edit Modal
        const editScheduleModal = document.getElementById('editScheduleModal');
        if (editScheduleModal) {
            editScheduleModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('edit_user_id').value = btn.dataset.userId;
                document.getElementById('edit_shift_name').value = btn.dataset.shiftName;
                document.getElementById('edit_start_time').value = btn.dataset.startTime;
                document.getElementById('edit_end_time').value = btn.dataset.endTime;
                document.getElementById('edit_notes').value = btn.dataset.notes || '';
                
                document.getElementById('edit_day_mon').checked = btn.dataset.isMonday === '1';
                document.getElementById('edit_day_tue').checked = btn.dataset.isTuesday === '1';
                document.getElementById('edit_day_wed').checked = btn.dataset.isWednesday === '1';
                document.getElementById('edit_day_thu').checked = btn.dataset.isThursday === '1';
                document.getElementById('edit_day_fri').checked = btn.dataset.isFriday === '1';
                document.getElementById('edit_day_sat').checked = btn.dataset.isSaturday === '1';
                document.getElementById('edit_day_sun').checked = btn.dataset.isSunday === '1';
                
                document.getElementById('edit_is_active').checked = btn.dataset.isActive === '1';

                document.getElementById('editScheduleForm').action = btn.dataset.updateUrl;
            });
        }
        // Live client-side search
        const searchInput = document.getElementById('scheduleSearchInput');
        const tbody       = document.querySelector('.table tbody');
        const noFilterRow = document.getElementById('noFilterResultsRow');

        function applyFilters() {
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const rows  = tbody ? tbody.querySelectorAll('tr[data-shift-name]') : [];
            let visible = 0;

            rows.forEach(function (row) {
                const matchSearch = !query ||
                    row.dataset.shiftName.includes(query) ||
                    row.dataset.employeeName.includes(query);
                row.style.display = matchSearch ? '' : 'none';
                if (matchSearch) { visible++; }
            });

            if (noFilterRow) {
                noFilterRow.style.display = (rows.length > 0 && visible === 0) ? '' : 'none';
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }
    })();
</script>
@endpush
