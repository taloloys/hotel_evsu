<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Models\ActivityLog;
use App\Models\Facility;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    public function index(): View
    {
        $facilities = Facility::orderBy('sort_order')
            ->orderBy('facility_id')
            ->get();

        return view('admin.facilities.index', compact('facilities'));
    }

    public function create(): View
    {
        return view('admin.facilities.create');
    }

    public function store(StoreFacilityRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $imagePaths = $this->handleImageUploads($request, []);

        Facility::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'capacity' => $validated['capacity'] ?? null,
            'rate' => $validated['rate'],
            'rate_type' => $validated['rate_type'],
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
            'images' => $imagePaths,
        ]);

        Cache::forget('public_showcase_data');
        ActivityLog::log('FACILITY_CREATED', "Created facility: {$validated['name']}.");

        return redirect()->route('admin.facilities.index')->with('success', 'Facility created successfully.');
    }

    public function edit(Facility $facility): View
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    public function update(UpdateFacilityRequest $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validated();

        // Handle image paths from hidden input (existing images that weren't removed)
        if (array_key_exists('image_paths', $validated)) {
            $existingPaths = ! empty($validated['image_paths'])
                ? array_filter(array_map('trim', explode(',', $validated['image_paths'])))
                : [];
        } else {
            $existingPaths = $facility->images ?? [];
        }

        $imagePaths = $this->handleImageUploads($request, $existingPaths);

        // Clean up removed images from disk
        $this->cleanupRemovedImages($facility->images ?? [], $imagePaths);

        $facility->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'capacity' => $validated['capacity'] ?? null,
            'rate' => $validated['rate'],
            'rate_type' => $validated['rate_type'],
            'is_active' => $validated['is_active'] ?? $facility->is_active,
            'sort_order' => $validated['sort_order'] ?? $facility->sort_order,
            'images' => $imagePaths,
        ]);

        Cache::forget('public_showcase_data');
        ActivityLog::log('FACILITY_UPDATED', "Updated facility: {$facility->name}.");

        return redirect()->route('admin.facilities.index')->with('success', 'Facility updated successfully.');
    }

    public function toggle(Facility $facility): RedirectResponse
    {
        $facility->update(['is_active' => ! $facility->is_active]);

        Cache::forget('public_showcase_data');
        ActivityLog::log('FACILITY_TOGGLED', "Toggled facility {$facility->name} active status.");

        return redirect()->route('admin.facilities.index')->with('success', 'Facility status updated.');
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        $title = $facility->name;

        $this->cleanupRemovedImages($facility->images ?? [], []);
        $facility->delete();

        Cache::forget('public_showcase_data');
        ActivityLog::log('FACILITY_DELETED', "Deleted facility: {$title}.");

        return redirect()->route('admin.facilities.index')->with('success', 'Facility deleted.');
    }

    /**
     * Compress and append newly uploaded images to existing paths.
     *
     * @param  array<string>  $existingPaths
     * @return array<string>
     */
    private function handleImageUploads(Request $request, array $existingPaths): array
    {
        $paths = $existingPaths;

        foreach ($request->file('images', []) as $file) {
            $paths[] = $this->imageService->compressAndStore($file, 'images/showcase/facilities', 1200, 800, 80);
        }

        return array_values(array_unique($paths));
    }

    /**
     * Delete images from disk that were removed during editing.
     *
     * @param  array<string>  $oldImages
     * @param  array<string>  $currentImages
     */
    private function cleanupRemovedImages(array $oldImages, array $currentImages): void
    {
        foreach (array_diff($oldImages, $currentImages) as $removed) {
            // Only delete uploaded images (not static repo assets)
            if (! file_exists(public_path($removed))) {
                $this->imageService->deleteImage($removed);
            }
        }
    }
}
