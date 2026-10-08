<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Models\ActivityLog;
use App\Models\Facility;
use App\Models\FacilitySet;
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
        $facilities = Facility::with('facilitySets')
            ->orderBy('name')
            ->paginate(10, ['*'], 'f_page');

        $facilitySets = FacilitySet::with('facilities')
            ->orderBy('name')
            ->paginate(10, ['*'], 's_page');

        return view('admin.facilities.index', compact('facilities', 'facilitySets'));
    }

    public function create(): View
    {
        $individualFacilities = Facility::orderBy('name')->get();

        return view('admin.facilities.create', compact('individualFacilities'));
    }

    public function store(StoreFacilityRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $imagePaths = $this->handleImageUploads($request, []);
        $isSet = ($validated['facility_type'] === 'set');

        // Resolve canonical rate and rate_type
        [$rate, $rateType, $hourlyRate, $dailyRate] = $this->resolveRates($validated);

        if ($isSet) {
            $set = FacilitySet::create([
                'name' => $validated['name'],
                'prefix_code' => $validated['prefix_code'] ?? null,
                'description' => $validated['description'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'hourly_rate' => $hourlyRate,
                'daily_rate' => $dailyRate,
                'rate' => $rate,
                'rate_type' => $rateType,
                'images' => $imagePaths,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $set->facilities()->sync($validated['member_facilities'] ?? []);

            Cache::forget('public_showcase_data');
            ActivityLog::log('FACILITY_CREATED', "Created facility set: {$validated['name']}.");

            return redirect()->route('admin.facilities.index')
                ->with('success', 'Facility set "'.$validated['name'].'" created successfully.');
        }

        Facility::create([
            'name' => $validated['name'],
            'prefix_code' => $validated['prefix_code'] ?? null,
            'description' => $validated['description'] ?? null,
            'capacity' => $validated['capacity'] ?? null,
            'hourly_rate' => $hourlyRate,
            'daily_rate' => $dailyRate,
            'rate' => $rate,
            'rate_type' => $rateType,
            'images' => $imagePaths,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => 0,
        ]);

        Cache::forget('public_showcase_data');
        ActivityLog::log('FACILITY_CREATED', "Created facility: {$validated['name']}.");

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility "'.$validated['name'].'" created successfully.');
    }

    public function edit(Request $request, string $id): View
    {
        $individualFacilities = Facility::orderBy('name')->get();

        // Support editing a FacilitySet by passing ?type=set
        if ($request->query('type') === 'set') {
            $facilitySet = FacilitySet::with('facilities')->findOrFail($id);

            return view('admin.facilities.edit', [
                'isSet' => true,
                'facilitySet' => $facilitySet,
                'facility' => null,
                'individualFacilities' => $individualFacilities,
            ]);
        }

        $facility = Facility::findOrFail($id);

        return view('admin.facilities.edit', [
            'isSet' => false,
            'facility' => $facility,
            'facilitySet' => null,
            'individualFacilities' => $individualFacilities,
        ]);
    }

    public function update(UpdateFacilityRequest $request, string $id): RedirectResponse
    {
        $validated = $request->validated();
        $isSet = ($validated['facility_type'] === 'set');

        [$rate, $rateType, $hourlyRate, $dailyRate] = $this->resolveRates($validated);

        if ($isSet) {
            $set = FacilitySet::findOrFail($id);

            // Handle images
            $existingPaths = $this->resolveExistingImagePaths($request, $validated, $set->images ?? []);
            $imagePaths = $this->handleImageUploads($request, $existingPaths);
            $this->cleanupRemovedImages($set->images ?? [], $imagePaths);

            $set->update([
                'name' => $validated['name'],
                'prefix_code' => $validated['prefix_code'] ?? null,
                'description' => $validated['description'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'hourly_rate' => $hourlyRate,
                'daily_rate' => $dailyRate,
                'rate' => $rate,
                'rate_type' => $rateType,
                'images' => $imagePaths,
                'is_active' => $request->boolean('is_active', $set->is_active),
            ]);

            $set->facilities()->sync($validated['member_facilities'] ?? []);

            Cache::forget('public_showcase_data');
            ActivityLog::log('FACILITY_UPDATED', "Updated facility set: {$set->name}.");

            return redirect()->route('admin.facilities.index')
                ->with('success', 'Facility set "'.$set->name.'" updated successfully.');
        }

        $facility = Facility::findOrFail($id);

        $existingPaths = $this->resolveExistingImagePaths($request, $validated, $facility->images ?? []);
        $imagePaths = $this->handleImageUploads($request, $existingPaths);
        $this->cleanupRemovedImages($facility->images ?? [], $imagePaths);

        $facility->update([
            'name' => $validated['name'],
            'prefix_code' => $validated['prefix_code'] ?? null,
            'description' => $validated['description'] ?? null,
            'capacity' => $validated['capacity'] ?? null,
            'hourly_rate' => $hourlyRate,
            'daily_rate' => $dailyRate,
            'rate' => $rate,
            'rate_type' => $rateType,
            'images' => $imagePaths,
            'is_active' => $request->boolean('is_active', $facility->is_active),
        ]);

        Cache::forget('public_showcase_data');
        ActivityLog::log('FACILITY_UPDATED', "Updated facility: {$facility->name}.");

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility "'.$facility->name.'" updated successfully.');
    }

    public function toggle(Request $request, string $id): RedirectResponse
    {
        if ($request->query('type') === 'set') {
            $set = FacilitySet::findOrFail($id);
            $set->update(['is_active' => ! $set->is_active]);
            Cache::forget('public_showcase_data');
            ActivityLog::log('FACILITY_TOGGLED', "Toggled facility set {$set->name} active status.");

            $label = $set->is_active ? 'enabled' : 'disabled';

            return redirect()->route('admin.facilities.index')
                ->with('success', 'Facility set "'.$set->name.'" has been '.$label.'.');
        }

        $facility = Facility::findOrFail($id);
        $facility->update(['is_active' => ! $facility->is_active]);
        Cache::forget('public_showcase_data');
        ActivityLog::log('FACILITY_TOGGLED', "Toggled facility {$facility->name} active status.");

        $label = $facility->is_active ? 'enabled' : 'disabled';

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility "'.$facility->name.'" has been '.$label.'.');
    }

    // Keep destroy signature for route compatibility, but redirect with error
    public function destroy(Facility $facility): RedirectResponse
    {
        return redirect()->route('admin.facilities.index')
            ->with('error', 'Deletion is disabled. Use the Enable/Disable toggle instead.');
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    /**
     * Derive canonical rate, rate_type, hourly_rate, daily_rate from form input.
     *
     * @return array{float, string, float|null, float|null}
     */
    private function resolveRates(array $validated): array
    {
        $rateType = $validated['rate_type']; // hourly | daily | both
        $hourlyRate = isset($validated['hourly_rate']) && $validated['hourly_rate'] !== '' ? (float) $validated['hourly_rate'] : null;
        $dailyRate = isset($validated['daily_rate']) && $validated['daily_rate'] !== '' ? (float) $validated['daily_rate'] : null;

        if ($rateType === 'both') {
            $rate = $hourlyRate ?? $dailyRate ?? 0.0;
            $canonical = $dailyRate !== null ? 'daily' : 'hourly';
        } elseif ($rateType === 'daily') {
            $rate = $dailyRate ?? 0.0;
            $canonical = 'daily';
        } else {
            $rate = $hourlyRate ?? 0.0;
            $canonical = 'hourly';
        }

        return [$rate, $canonical, $hourlyRate, $dailyRate];
    }

    /**
     * @param  array<string>  $fallback
     * @return array<string>
     */
    private function resolveExistingImagePaths(Request $request, array $validated, array $fallback): array
    {
        if (array_key_exists('image_paths', $validated)) {
            return ! empty($validated['image_paths'])
                ? array_filter(array_map('trim', explode(',', $validated['image_paths'])))
                : [];
        }

        return $fallback;
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
            if (! file_exists(public_path($removed))) {
                $this->imageService->deleteImage($removed);
            }
        }
    }
}
