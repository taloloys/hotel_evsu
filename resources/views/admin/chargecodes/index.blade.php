@extends('layouts.app')

@section('title', 'Charge Codes')
@section('pageTitle', 'Charge Codes')
@section('pageSubtitle', 'Manage hotel billing charge codes, descriptions, and categories')

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
        <div id="validationToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <strong>Error:</strong> {{ $errors->first() }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

</div>

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Total Charge Codes</div>
                    <h4 class="mb-0 fw-bold">{{ $totalCount }}</h4>
                </div>
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-receipt fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Active</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $activeCount }}</h4>
                </div>
                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-circle-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Inactive</div>
                    <h4 class="mb-0 fw-bold text-danger">{{ $inactiveCount }}</h4>
                </div>
                <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-circle-xmark fs-4"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- PAGE HEADER + ACTIONS BAR -->
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">

    <!-- TITLE -->
    <div>
        <h5 class="fw-bold mb-0">Charge Codes</h5>
        <small class="text-muted">Manage charge codes, categories, and status</small>
    </div>

    <!-- RIGHT ACTIONS -->
    <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">

        <!-- SEARCH -->
        <div style="width: 340px;">
            <div class="input-group"
                 style="border: 1px solid #000000; border-radius: 6px; overflow: hidden; height: 38px;">

                <span class="input-group-text bg-white border-0">
                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                </span>

                <input type="text"
                       id="chargeSearchInput"
                       class="form-control border-0 shadow-none"
                       placeholder="Search code or description..."
                       autocomplete="off">
            </div>
        </div>

        <!-- FILTER -->
        <div class="dropdown">

            <button class="btn btn-outline-secondary d-flex align-items-center gap-1 px-3"
                    data-bs-toggle="dropdown"
                    style="height: 38px; border-radius: 6px;">

                <i class="fa-solid fa-filter"></i>
                <span>Filter</span>

            </button>

            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 style="min-width: 280px; border-radius: 8px;">

                <label class="form-label small mb-1 fw-semibold">Category</label>
                <select id="filterCategorySelect"
                        class="form-select mb-3"
                        style="height: 38px; border-radius: 6px;">

                    <option value="">All Categories</option>

                    @foreach($categories as $category)
                        <option value="{{ $category }}">
                            {{ str_replace('_', ' ', $category) }}
                        </option>
                    @endforeach

                </select>

                <label class="form-label small mb-1 fw-semibold">Status</label>
                <select id="filterStatusSelect"
                        class="form-select mb-3"
                        style="height: 38px; border-radius: 6px;">

                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="disabled">Inactive</option>

                </select>

                <div class="d-flex gap-2">
                    <button id="filterApplyBtn" class="btn btn-primary w-50" style="height: 38px;">
                        Apply
                    </button>

                    <button id="filterResetBtn" class="btn btn-light w-50" style="height: 38px;">
                        Reset
                    </button>
                </div>

            </div>
        </div>

        <!-- ADD CHARGE -->
        <button class="btn btn-primary d-flex align-items-center gap-2 px-3"
                style="height: 38px; border-radius: 6px;"
                data-bs-toggle="modal"
                data-bs-target="#addChargeModal">

            <i class="fa-solid fa-plus"></i>
            <span>Add Charge</span>

        </button>

    </div>
</div>


<!-- TABLE SECTION (FIXED OUTSIDE HEADER) -->
<div class="card border-0 shadow-sm">

    <div class="table-responsive">

        <table id="chargeCodesTable" class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th class="ps-3" style="width: 120px;">Code</th>
                    <th>Charge Name / Description</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 140px;">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($chargeCodes as $charge)
                    <tr data-code="{{ $charge->charge_code }}"
                        data-description="{{ strtolower($charge->description) }}"
                        data-category="{{ strtolower($charge->category) }}"
                        data-active="{{ $charge->is_active ? 'active' : 'disabled' }}"
                        @if(!$charge->is_active) class="opacity-75 bg-light-subtle" @endif>

                        <td class="ps-3 fw-bold text-primary">
                            {{ $charge->charge_code }}
                        </td>

                        <td class="fw-semibold">
                            <span class="text-muted me-2">[{{ $charge->charge_code }}]</span>
                            {{ $charge->description }}
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ str_replace('_', ' ', $charge->category) }}
                            </span>
                        </td>

                        <td>
                            @if($charge->is_active)
                                <span class="badge bg-success">
                                    <i class="fa-solid fa-circle-check me-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fa-solid fa-circle-xmark me-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">

                                <!-- EDIT -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editChargeModal"
                                        data-charge-code="{{ $charge->charge_code }}"
                                        data-description="{{ $charge->description }}"
                                        data-category="{{ $charge->category }}"
                                        data-update-url="{{ route('admin.chargecodes.update', $charge) }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <!-- TOGGLE -->
                                <form action="{{ route('admin.chargecodes.toggle', $charge) }}"
                                      method="POST"
                                      class="d-inline m-0">
                                    @csrf
                                    @method('PATCH')

                                    @if($charge->is_active)
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    @endif
                                </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="noResultsRow">
                        <td colspan="5" class="text-center py-4 text-muted">
                            No charge codes found in the system.
                        </td>
                    </tr>
                @endforelse

                <tr id="noFilterResultsRow" style="display:none;">
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fa-solid fa-magnifying-glass me-2"></i>
                        No charge codes match your search or filter.
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

@if($chargeCodes->hasPages())
<div class="d-flex justify-content-between align-items-center px-1 py-2 mt-1">
    <small class="text-muted">
        Showing {{ $chargeCodes->firstItem() }}–{{ $chargeCodes->lastItem() }} of {{ $chargeCodes->total() }} charge codes
    </small>
    <div>
        {{ $chargeCodes->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<!-- =========================================================
     ADD CHARGE MODAL
     ========================================================= -->
<div class="modal fade" id="addChargeModal" tabindex="-1" aria-labelledby="addChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addChargeModalLabel">
                    <i class="fa-solid fa-plus me-2 text-primary"></i>Add Charge Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.chargecodes.store') }}">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="add_charge_code" class="form-label">Charge Code</label>
                        <input type="number" id="add_charge_code" name="charge_code" class="form-control" placeholder="e.g. 105" required min="1">
                        <small class="text-muted">Must be a unique numeric identifier.</small>
                    </div>

                    <div class="mb-3">
                        <label for="add_description" class="form-label">Charge Name / Description</label>
                        <input type="text" id="add_description" name="description" class="form-control" placeholder="e.g. LAUNDRY SERVICE" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_category" class="form-label">Category</label>
                        <select id="add_category" name="category" class="form-select" required>
                            <option value="HOTEL">HOTEL</option>
                            <option value="RESTAURANT">RESTAURANT</option>
                            <option value="TAX_SERVICE">TAX_SERVICE</option>
                            <option value="PAYMENT">PAYMENT</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i>Save Charge
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- =========================================================
     EDIT CHARGE MODAL
     ========================================================= -->
<div class="modal fade" id="editChargeModal" tabindex="-1" aria-labelledby="editChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editChargeModalLabel">
                    <i class="fa-solid fa-pen me-2 text-warning"></i>Edit Charge Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" id="editChargeForm" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Charge Code</label>
                        <input type="text" id="edit_charge_code" class="form-control bg-light" disabled>
                        <small class="text-muted">Charge code identifier cannot be changed.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Charge Name / Description</label>
                        <input type="text" id="edit_description" name="description" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_category" class="form-label">Category</label>
                        <select id="edit_category" name="category" class="form-select" required>
                            <option value="HOTEL">HOTEL</option>
                            <option value="RESTAURANT">RESTAURANT</option>
                            <option value="TAX_SERVICE">TAX_SERVICE</option>
                            <option value="PAYMENT">PAYMENT</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fa-solid fa-save me-1"></i>Save Changes
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
        // Show toast notifications
        document.querySelectorAll('.toast').forEach(function (el) {
            new bootstrap.Toast(el).show();
        });

        // Populate Edit Charge Modal
        const editChargeModal = document.getElementById('editChargeModal');
        if (editChargeModal) {
            editChargeModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('edit_charge_code').value = btn.dataset.chargeCode;
                document.getElementById('edit_description').value = btn.dataset.description;
                document.getElementById('edit_category').value    = btn.dataset.category;
                document.getElementById('editChargeForm').action  = btn.dataset.updateUrl;
            });
        }

        // Client-side search and filtering
        const searchInput      = document.getElementById('chargeSearchInput');
        const categorySelect   = document.getElementById('filterCategorySelect');
        const statusSelect     = document.getElementById('filterStatusSelect');
        const applyBtn         = document.getElementById('filterApplyBtn');
        const resetBtn         = document.getElementById('filterResetBtn');
        const tbody            = document.querySelector('#chargeCodesTable tbody');
        const noFilterRow      = document.getElementById('noFilterResultsRow');
        const dropdownToggleEl = document.querySelector('.dropdown [data-bs-toggle="dropdown"]');

        let activeCategory = '';
        let activeStatus = '';

        function applyFilters() {
            const query = searchInput.value.trim().toLowerCase();
            const rows  = tbody.querySelectorAll('tr[data-code]');
            let visible = 0;

            rows.forEach(function (row) {
                const matchSearch = !query || 
                                    row.dataset.code.includes(query) || 
                                    row.dataset.description.includes(query);
                const matchCat    = !activeCategory || row.dataset.category === activeCategory.toLowerCase();
                const matchStatus = !activeStatus || row.dataset.active === activeStatus;

                const show = matchSearch && matchCat && matchStatus;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (noFilterRow) {
                noFilterRow.style.display = (rows.length > 0 && visible === 0) ? '' : 'none';
            }
        }

        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        if (searchInput) {
            searchInput.addEventListener('input', debounce(applyFilters, 400));
        }

        if (applyBtn) {
            applyBtn.addEventListener('click', function () {
                activeCategory = categorySelect.value;
                activeStatus   = statusSelect.value;
                applyFilters();
                if (dropdownToggleEl) {
                    const dropdown = bootstrap.Dropdown.getInstance(dropdownToggleEl);
                    if (dropdown) { dropdown.hide(); }
                }
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                searchInput.value     = '';
                categorySelect.value  = '';
                statusSelect.value    = '';
                activeCategory        = '';
                activeStatus          = '';
                applyFilters();
            });
        }
    })();
</script>
@endpush