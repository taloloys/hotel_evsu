@extends('layouts.app')

@section('title', 'Registration - Don Felipe Hotel')
@section('pageTitle', 'Guest Registration')
@section('pageSubtitle', 'Register guests and assign rooms.')

@section('content')

<div class="container-fluid">

    <form>

        <!-- Registration Information -->

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Registration Information</h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Guest Folio Number</label>
                        <input type="text" class="form-control" placeholder="Auto Generated">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Registration Number</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Account Number</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Reservation Number</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Temporary Folio Number</label>
                        <input type="text" class="form-control">
                    </div>

                </div>

            </div>
        </div>

        <!-- Guest Information -->

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Guest Information</h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Group Name</label>
                        <input type="text" class="form-control">
                    </div>

                </div>

            </div>
        </div>

        <!-- Stay Information -->

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Stay Information</h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Arrival Date</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Arrival Time</label>
                        <input type="time" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Departure Date</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Departure Time</label>
                        <input type="time" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">No. of Pax</label>
                        <input type="number" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Joiner</label>
                        <select class="form-select">
                            <option>No</option>
                            <option>Yes</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Market Segment</label>
                        <select class="form-select">
                            <option>NONE</option>
                            <option>Corporate</option>
                            <option>Government</option>
                            <option>Walk-in</option>
                            <option>Travel Agency</option>
                        </select>
                    </div>

                </div>

            </div>
        </div>

        <!-- Room Information -->

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Room Information</h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Room Type</label>
                        <select class="form-select">
                            <option>Economy</option>
                            <option>Standard</option>
                            <option>Deluxe</option>
                            <option>Suite</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Room Number</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Rate Type</label>
                        <select class="form-select">
                            <option>Daily</option>
                            <option>Weekly</option>
                            <option>Monthly</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Symbol</label>
                        <select class="form-select">
                            <option>CBO</option>
                            <option>VIP</option>
                            <option>CORP</option>
                        </select>
                    </div>

                    <div class="col-md-8 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox">
                            <label class="form-check-label">
                                Room Rate to Master Folio
                            </label>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Charges & Billing -->

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Charges & Billing</h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Room Rate</label>
                        <input type="number" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Government Tax</label>
                        <input type="number" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Service Charge</label>
                        <input type="number" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Total Room Rate / Day</label>
                        <input type="number" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Discount (%)</label>
                        <input type="number" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">No. of Extra Beds</label>
                        <input type="number" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Billing Arrangement</label>
                        <input type="text" class="form-control">
                    </div>

                </div>

            </div>
        </div>

        <!-- Additional Information -->

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Additional Information</h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Special Arrangements</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Upgrade / Downgrade</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">No. of Free Breakfasts</label>
                        <input type="number" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">BF Code</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">AE</label>
                        <select class="form-select">
                            <option>NONE</option>
                            <option>YES</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Filter Reservation Arrivals</label>
                        <select class="form-select">
                            <option>Today</option>
                            <option>Tomorrow</option>
                            <option>This Week</option>
                        </select>
                    </div>

                </div>

            </div>
        </div>

        <!-- Action Buttons -->

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-end gap-2">

                    <button type="button" class="btn btn-secondary">
                        New
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Save Registration
                    </button>

                    <button type="button" class="btn btn-warning">
                        Update
                    </button>

                    <button type="reset" class="btn btn-danger">
                        Cancel
                    </button>

                </div>

            </div>
        </div>

    </form>

</div>

@endsection