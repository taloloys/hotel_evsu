@extends('layouts.app')

@section('title', 'Landing Page & Showcase Control')
@section('pageTitle', 'Landing Page Showcase Management')
@section('pageSubtitle', 'Control public landing page rooms, pricing per night, cafeteria hero image, and highlights')

@section('content')

{{-- TOAST CONTAINER --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    @if(session('success'))
        <div id="successToast" class="toast align-items-center text-white bg-success border-0 shadow show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 shadow show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first() }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif
</div>

<div class="container-fluid px-0 space-y-6">

    <!-- TOP CONTROL BAR -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 bg-white p-4 rounded-4 shadow-sm border border-secondary border-opacity-10">
        <div>
            <h4 class="fw-bold text-dark mb-1">Public Showcase Landing Page Control</h4>
            <p class="text-secondary mb-0 small">Customize room catalog titles, rates per night, multiple uploaded images, and cafeteria showcase assets without hardcoding.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.rooms') }}" class="btn btn-primary rounded-3 px-3">
                <i class="fa-solid fa-bed me-2"></i>Manage Hotel Rooms
            </a>
            <button class="btn btn-outline-secondary rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#addCafeteriaItemModal">
                <i class="fa-solid fa-mug-hot me-2"></i>Add Cafeteria Item
            </button>
            <a href="{{ url('/') }}" target="_blank" class="btn btn-dark rounded-3 px-3">
                <i class="fa-solid fa-eye me-2"></i>View Live Page
            </a>
        </div>
    </div>

    <!-- MAIN CAFETERIA HERO IMAGE CONTROL CARD -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-dark text-white p-4 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-1"><i class="fa-solid fa-store me-2 text-warning"></i>Main Cafeteria Showcase Hero Image & Details</h5>
                <small class="text-white-50">Set the primary featured photograph and timing info for the Cafeteria section.</small>
            </div>
            <button class="btn btn-sm btn-light font-weight-bold" data-bs-toggle="collapse" data-bs-target="#cafeteriaMainCollapse">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Main Image
            </button>
        </div>
        <div class="collapse show" id="cafeteriaMainCollapse">
            <div class="card-body p-4 bg-light">
                <form method="POST" action="{{ route('admin.landing-page.cafeteria-main.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 items-center">
                        <div class="col-md-3 text-center">
                            @php
                                $mainImg = !empty($cafeteriaMain->images[0]) ? $cafeteriaMain->images[0] : 'images/showcase/coffeeshop/cafeteria_main.jpg';
                                $hasMainImg = file_exists(public_path($mainImg));
                            @endphp
                            <div class="rounded-3 overflow-hidden border border-secondary border-opacity-25 bg-white shadow-sm h-100 d-flex items-center justify-center p-2" style="min-height: 140px;">
                                @if($hasMainImg)
                                    <img src="{{ asset($mainImg) }}" alt="Cafeteria Main" class="img-fluid rounded" style="max-height: 130px; object-fit: cover;">
                                @else
                                    <div class="text-muted p-3">
                                        <i class="fa-solid fa-mug-hot fa-2x mb-2 text-warning"></i>
                                        <div class="small fw-bold">Main Image Placeholder</div>
                                        <span class="badge bg-secondary">Upload Below</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Cafeteria Section Title</label>
                                    <input type="text" name="title" value="{{ old('title', $cafeteriaMain->title ?? 'Savor Handcrafted Coffee & Gourmet Culinary Treats') }}" required class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Category Tagline</label>
                                    <input type="text" name="category" value="{{ old('category', $cafeteriaMain->category ?? 'Don Felipe Cafeteria & Lounge') }}" required class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Operating Hours / Timing</label>
                                    <input type="text" name="timing" value="{{ old('timing', $cafeteriaMain->timing ?? 'Open daily 6:30 AM - 10:00 PM') }}" required class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Upload Main Cafeteria Image</label>
                                    <input type="file" name="image" accept="image/*" class="form-control">
                                </div>
                                <div class="col-12 text-end pt-2">
                                    <button type="submit" class="btn btn-warning font-weight-bold px-4">
                                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Cafeteria Showcase
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SHOWCASE ROOMS TABLE / CATALOG -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white p-4 border-bottom border-light">
            <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-bed me-2 text-primary"></i>Showcase Rooms Catalog ({{ count($rooms) }})</h5>
            <small class="text-secondary">Admin controls room pricing per night, title, category, and multiple image gallery paths.</small>
        </div>
        <div class="card-body bg-light border-bottom border-light py-3 px-4">
            <div class="alert alert-info border-0 rounded-3 mb-0 d-flex align-items-center gap-3">
                <i class="fa-solid fa-circle-info fs-4 text-info"></i>
                <div>
                    <span class="fw-bold d-block text-dark">Database Sync & Image Customization Note</span>
                    <span class="small text-secondary">The landing page dynamically connects each room category to active database rooms by matching the <b>Room Name / Title</b> with the <b>Room Type</b> (e.g., <i>Deluxe Room</i> or <i>Suite</i>). Room pricing is synced automatically with database rates. Add images and details here, ensuring the title matches your database Room Type!</span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Preview & Images</th>
                        <th>Room Name / Title</th>
                        <th>Category</th>
                        <th>Price Rate / Night</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        @php
                            $dbRate = \App\Models\Room::where('room_type', $room->title)->where('is_active', true)->first()?->base_rate;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    @php
                                        $imgs = is_array($room->images) ? $room->images : [];
                                        $firstImg = $imgs[0] ?? null;
                                        $hasImg = $firstImg && file_exists(public_path($firstImg));
                                    @endphp
                                    @if($hasImg)
                                        <img src="{{ asset($firstImg) }}" alt="{{ $room->title }}" class="rounded border" style="width: 60px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="rounded border bg-light text-center p-1 text-muted" style="width: 60px; height: 45px; font-size: 10px;">
                                            <i class="fa-solid {{ $room->icon ?? 'fa-bed' }}"></i>
                                            <div class="small">SVG Card</div>
                                        </div>
                                    @endif
                                    <span class="badge bg-secondary font-monospace">{{ count($imgs) }} img(s)</span>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $room->title }}</span>
                                @if(isset($room->badge) && $room->badge)
                                    <span class="badge bg-warning text-dark">{{ $room->badge }}</span>
                                @endif
                            </td>
                            <td><span class="text-secondary">{{ $room->category }}</span></td>
                            <td>
                                <span class="fw-bold text-success">
                                    @if($dbRate)
                                        ₱{{ number_format($dbRate, 2) }} / night
                                    @else
                                        —
                                    @endif
                                </span>
                            </td>
                            <td><span class="text-dark small">{{ $room->capacity }}</span></td>
                            <td>
                                @if($room->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-primary font-weight-bold" data-bs-toggle="modal" data-bs-target="#editRoomModal{{ $room->id }}">
                                    <i class="fa-solid fa-images me-1"></i>Configure & Upload
                                </button>
                            </td>
                        </tr>

                        <!-- EDIT ROOM MODAL -->
                        <div class="modal fade" id="editRoomModal{{ $room->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-header bg-dark text-white p-4">
                                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Configure Showcase - {{ $room->title }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.landing-page.room.update', $room->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold font-monospace text-secondary">Room Title / Name (From DB Rooms)</label>
                                                    <input type="text" readonly class="form-control bg-light fw-bold" value="{{ $room->title }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Category</label>
                                                    <input type="text" name="category" value="{{ old('category', $room->category) }}" required class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold font-monospace text-secondary">Pricing per Night (DB Rate)</label>
                                                    <input type="text" readonly class="form-control bg-light text-success fw-bold" value="@if($dbRate) ₱{{ number_format($dbRate, 2) }} / night @else — @endif">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Capacity</label>
                                                    <input type="text" name="capacity" value="{{ old('capacity', $room->capacity) }}" required class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Badge Overlay</label>
                                                    <input type="text" name="badge" value="{{ old('badge', $room->badge) }}" class="form-control">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Upload Room Images (Supports Multiple Files)</label>
                                                    <input type="file" name="images[]" multiple accept="image/*" class="form-control">
                                                    <div class="form-text">Uploading files will add them to this room's showcase image carousel.</div>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">Current Showcase Images</label>
                                                    <div class="d-flex flex-wrap gap-2 p-3 rounded bg-light border border-secondary border-opacity-10 mb-2">
                                                        @forelse($room->images ?? [] as $img)
                                                            @php
                                                                $hasImgFile = file_exists(public_path($img));
                                                            @endphp
                                                            <div class="position-relative border rounded p-1 bg-white shadow-sm text-center" style="width: 110px;">
                                                                @if($hasImgFile)
                                                                    <img src="{{ asset($img) }}" alt="Preview" class="rounded w-100 mb-1" style="height: 70px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-secondary-subtle rounded d-flex flex-column align-items-center justify-content-center text-muted mb-1" style="height: 70px; font-size: 8px;">
                                                                        <i class="fa-solid fa-triangle-exclamation text-warning mb-1"></i>
                                                                        <span>Not Found</span>
                                                                    </div>
                                                                @endif
                                                                <span class="d-block text-truncate text-secondary font-monospace" style="font-size: 9px;" title="{{ basename($img) }}">{{ basename($img) }}</span>
                                                            </div>
                                                        @empty
                                                            <span class="text-muted small">No images uploaded for this room type.</span>
                                                        @endforelse
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Current Image Paths (Comma-separated)</label>
                                                    <input type="text" name="image_paths" value="{{ old('image_paths', implode(', ', $room->images ?? [])) }}" class="form-control">
                                                    <div class="form-text text-muted">To remove any image from this room, delete its path from the comma-separated text list above.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer p-4 bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary font-weight-bold">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No showcase rooms configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>



<!-- ADD CAFETERIA ITEM MODAL -->
<div class="modal fade" id="addCafeteriaItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-mug-hot me-2"></i>Add Cafeteria Item Highlight</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.landing-page.cafeteria-item.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 space-y-3">
                    <div>
                        <label class="form-label small fw-bold">Item Title</label>
                        <input type="text" name="title" required placeholder="e.g. Artisan Espresso" class="form-control">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">Category</label>
                        <input type="text" name="category" required placeholder="e.g. Coffee & Beverages" class="form-control">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">Timing / Hours</label>
                        <input type="text" name="timing" required placeholder="e.g. Served All Day" class="form-control">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">Upload Image</label>
                        <input type="file" name="image" accept="image/*" class="form-control">
                    </div>
                </div>
                <div class="modal-footer p-4 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning font-weight-bold">Save Cafeteria Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
