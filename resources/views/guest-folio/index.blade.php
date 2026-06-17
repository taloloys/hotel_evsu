@extends('layouts.app')

@section('title', 'Guest Folio - Don Felipe Hotel')
@section('pageTitle', 'Guest Folio')
@section('pageSubtitle', 'Manage guest folios, billing, transfers and checkout.')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <!-- Header -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h5 class="fw-bold mb-1">
                    Guest Folios
                </h5>

                <small class="text-muted">
                    View guest folios, balances, room transfers and checkout records.
                </small>

            </div>

        </div>

        <!-- Filters -->

        <div class="card bg-light border-0 mb-4">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-lg-3">

                        <label class="form-label fw-semibold">
                            View Options
                        </label>

                        <select class="form-select">
                            <option>All Folios</option>
                            <option>Checked In</option>
                            <option>Checked Out</option>
                            <option>Open Folios</option>
                        </select>

                    </div>

                    <div class="col-lg-3">

                        <label class="form-label fw-semibold">
                            Search Options
                        </label>

                        <select class="form-select">
                            <option>Guest Name</option>
                            <option>Folio Number</option>
                            <option>Room Number</option>
                        </select>

                    </div>

                    <div class="col-lg-4">

                        <label class="form-label fw-semibold">
                            Search Text
                        </label>

                        <input type="text"
                               class="form-control"
                               placeholder="Enter search value">

                    </div>

                </div>

            </div>

        </div>

        <!-- Table -->

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>Room No.</th>
                        <th>Type</th>
                        <th>Folio No.</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Arrival</th>
                        <th>Departure</th>
                        <th>Net Rate</th>
                        <th>Symbol</th>
                        <th>Status</th>
                        <th width="170">Action</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>201</td>
                        <td>ECA</td>
                        <td>20102411</td>
                        <td>Albor</td>
                        <td>Clifford</td>
                        <td>06/15/2026</td>
                        <td>06/16/2026</td>
                        <td>950.00</td>
                        <td>CBO</td>

                        <td>
                            <span class="badge bg-success">
                                Checked In
                            </span>
                        </td>

                        <td>

                            <div class="btn-group btn-group-sm">

                                <button class="btn btn-outline-primary">
                                    Details
                                </button>

                                <button class="btn btn-outline-warning">
                                    Transfer
                                </button>

                            </div>

                        </td>

                    </tr>

                    <tr>

                        <td>329</td>
                        <td>ECA</td>
                        <td>20102414</td>
                        <td>Alipin</td>
                        <td>Evangeline</td>
                        <td>06/13/2026</td>
                        <td>06/12/2026</td>
                        <td>950.00</td>
                        <td>CBO</td>

                        <td>
                            <span class="badge bg-secondary">
                                Open
                            </span>
                        </td>

                        <td>

                            <div class="btn-group btn-group-sm">

                                <button class="btn btn-outline-primary">
                                    Details
                                </button>

                                <button class="btn btn-outline-warning">
                                    Transfer
                                </button>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <!-- Footer Actions -->

        <div class="d-flex justify-content-between align-items-center mt-4">

            <small class="text-muted">
                Showing 1 - 2 of 10 folio records
            </small>

            <div class="d-flex gap-2">

                <button class="btn btn-outline-info">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Details
                </button>

                <button class="btn btn-outline-warning">
                    <i class="fa-solid fa-right-left me-1"></i>
                    Room Transfer
                </button>

                <button class="btn btn-outline-secondary">
                    <i class="fa-solid fa-print me-1"></i>
                    Print Folio
                </button>

                <button class="btn btn-success">
                    <i class="fa-solid fa-door-open me-1"></i>
                    Checkout
                </button>

            </div>

        </div>

    </div>

</div>

@endsection