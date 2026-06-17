@extends('layouts.app')

@section('title', 'Guest List - Don Felipe Hotel')
@section('pageTitle', 'Guest List')
@section('pageSubtitle', 'In-House Guest List Report')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        In-House Guest List
                    </h5>
                </div>

                <div class="card-body">

                    <fieldset class="border rounded p-4">

                        <legend class="float-none w-auto px-2 fs-6">
                            Report Criteria
                        </legend>

                        <!-- Room Numbers -->
                        <div class="row mb-4">

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Room Numbers
                                </label>
                            </div>

                            <div class="col-md-9">

                                <div class="row mb-2">

                                    <div class="col-md-2 d-flex align-items-center">
                                        <label>From</label>
                                    </div>

                                    <div class="col-md-10">

                                        <select class="form-select">

                                            <option>201 ECONOMY AIRCON-201</option>
                                            <option>202 ECONOMY AIRCON-202</option>
                                            <option>203 ECONOMY AIRCON-203</option>
                                            <option>204 FAMILY ROOM-204</option>
                                            <option>205 ECONOMY AIRCON-205</option>
                                            <option>206 ECONOMY AIRCON-206</option>
                                            <option>207 ECONOMY AIRCON-207</option>
                                            <option>208 ECONOMY AIRCON-208</option>
                                            <option>209 ECONOMY AIRCON-209</option>
                                            <option>210 ECONOMY AIRCON-210</option>
                                            <option>211 ECONOMY AIRCON-211</option>
                                            <option>212 ECONOMY AIRCON-212</option>
                                            <option>213 ECONOMY AIRCON-213</option>
                                            <option>301 STANDARD-301</option>
                                            <option>302 JUNIOR SUITE-302</option>

                                        </select>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-2 d-flex align-items-center">
                                        <label>Until</label>
                                    </div>

                                    <div class="col-md-10">

                                        <select class="form-select">

                                            <option>201 ECONOMY AIRCON-201</option>
                                            <option>202 ECONOMY AIRCON-202</option>
                                            <option>203 ECONOMY AIRCON-203</option>
                                            <option>204 FAMILY ROOM-204</option>
                                            <option>205 ECONOMY AIRCON-205</option>
                                            <option>206 ECONOMY AIRCON-206</option>
                                            <option>207 ECONOMY AIRCON-207</option>
                                            <option>208 ECONOMY AIRCON-208</option>
                                            <option>209 ECONOMY AIRCON-209</option>
                                            <option>210 ECONOMY AIRCON-210</option>
                                            <option>211 ECONOMY AIRCON-211</option>
                                            <option>212 ECONOMY AIRCON-212</option>
                                            <option>213 ECONOMY AIRCON-213</option>
                                            <option>301 STANDARD-301</option>
                                            <option>302 JUNIOR SUITE-302</option>

                                        </select>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Shift -->
                        <div class="row mb-4">

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Shift No.
                                </label>
                            </div>

                            <div class="col-md-9">

                                <div class="form-check">

                                    <input class="form-check-input"
                                           type="radio"
                                           name="shift"
                                           id="shift1"
                                           checked>

                                    <label class="form-check-label"
                                           for="shift1">
                                        1st
                                    </label>

                                </div>

                            </div>

                        </div>

                        <!-- Checkboxes -->
                        <div class="row mb-4">

                            <div class="col-md-3"></div>

                            <div class="col-md-9">

                                <div class="form-check mb-2">

                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="companion">

                                    <label class="form-check-label"
                                           for="companion">

                                        Include Guest's Companion

                                    </label>

                                </div>

                                <div class="form-check">

                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="roomrate">

                                    <label class="form-check-label"
                                           for="roomrate">

                                        Include Room Rate

                                    </label>

                                </div>

                            </div>

                        </div>

                        <!-- Button -->
                        <div class="text-end">

                            <button type="button"
                                    class="btn btn-primary">

                                Generate Report

                            </button>

                        </div>

                    </fieldset>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection