@extends('layouts.app')

@section('title', 'Shift Sales - Don Felipe Hotel')
@section('pageTitle', 'Shift Sales')
@section('pageSubtitle', 'Generate and view shift sales reports.')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-4">
                        Shift Detail Report
                    </h4>

                    <form action="#" method="GET">

                        <!-- Hotel Charge Codes -->

                        <div class="row mb-3 align-items-center">

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Hotel Charge Codes
                                </label>
                            </div>

                            <div class="col-md-4">
                                <select class="form-select" name="charge_code_from">
                                    <option value="">From</option>
                                    <option>Room Charges</option>
                                    <option>Restaurant Charges</option>
                                    <option>Bar Charges</option>
                                    <option>Laundry Charges</option>
                                </select>
                            </div>

                            <div class="col-md-1 text-center">
                                Until
                            </div>

                            <div class="col-md-4">
                                <select class="form-select" name="charge_code_until">
                                    <option value="">Select</option>
                                    <option>Room Charges</option>
                                    <option>Restaurant Charges</option>
                                    <option>Bar Charges</option>
                                    <option>Laundry Charges</option>
                                </select>
                            </div>

                        </div>

                        <!-- Transaction Dates -->

                        <div class="row mb-3 align-items-center">

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Transaction Dates
                                </label>
                            </div>

                            <div class="col-md-4">
                                <input
                                    type="date"
                                    class="form-control"
                                    name="date_from">
                            </div>

                            <div class="col-md-1 text-center">
                                Until
                            </div>

                            <div class="col-md-4">
                                <input
                                    type="date"
                                    class="form-control"
                                    name="date_until">
                            </div>

                        </div>

                        <!-- Employee ID -->

                        <div class="row mb-3 align-items-center">

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Employee ID
                                </label>
                            </div>

                            <div class="col-md-9">
                                <select class="form-select" name="employee_id">
                                    <option value="">Select Employee</option>
                                    <option>EMP-001</option>
                                    <option>EMP-002</option>
                                    <option>EMP-003</option>
                                </select>
                            </div>

                        </div>

                        <!-- Report Type -->

                        <div class="row mb-4">

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Report Type
                                </label>
                            </div>

                            <div class="col-md-9">

                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="report_type"
                                        value="hotel"
                                        checked>

                                    <label class="form-check-label">
                                        Hotel
                                    </label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="report_type"
                                        value="restaurant">

                                    <label class="form-check-label">
                                        Restaurant
                                    </label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="report_type"
                                        value="all">

                                    <label class="form-check-label">
                                        All
                                    </label>
                                </div>

                            </div>

                        </div>

                        <!-- Buttons -->

                        <div class="text-end">

                            <button type="submit" class="btn btn-primary">
                                View Report
                            </button>

                            <button
                                type="button"
                                class="btn btn-secondary"
                                onclick="window.history.back()">
                                Close
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection