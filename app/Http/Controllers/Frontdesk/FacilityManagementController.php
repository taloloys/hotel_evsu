<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\FacilitySet;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FacilityManagementController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    public function index(): View
    {
        $facilities = Facility::with('facilitySets')
            ->withCount('reservations')
            ->orderBy('name')
            ->get();

        $facilitySets = FacilitySet::with('facilities')
            ->withCount('reservations')
            ->orderBy('name')
            ->get();

        $pendingCount = FacilityReservation::where('status', 'pending')->count();

        return view('frontdesk.facilities.index', compact('facilities', 'facilitySets', 'pendingCount'));
    }

    public function create(): View
    {
        $individualFacilities = Facility::orderBy('name')->get();

        return view('frontdesk.facilities.create', compact('individualFacilities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'facility_type' => ['required', 'in:single,set'],
            'name' => ['required', 'string', 'max:255'],
            'prefix_code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'daily_rate' => ['nullable', 'numeric', 'min:0'],
            'rate_type' => ['required', 'in:hourly,daily,both'],
            'is_active' => ['boolean'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'member_facilities' => ['nullable', 'array'],
            'member_facilities.*' => ['integer', 'exists:facilities,facility_id'],
        ]);

        $imagePaths = $this->handleImageUploads($request, []);
        $isSet = ($validated['facility_type'] === 'set');

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

            return redirect()->route('frontdesk.facilities.index')
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

        return redirect()->route('frontdesk.facilities.index')
            ->with('success', 'Facility "'.$validated['name'].'" created successfully.');
    }

    public function edit(Request $request, string $id): View
    {
        $individualFacilities = Facility::orderBy('name')->get();

        if ($request->query('type') === 'set') {
            $facilitySet = FacilitySet::with('facilities')->findOrFail($id);

            return view('frontdesk.facilities.edit', [
                'isSet' => true,
                'facilitySet' => $facilitySet,
                'facility' => null,
                'individualFacilities' => $individualFacilities,
            ]);
        }

        $facility = Facility::findOrFail($id);

        return view('frontdesk.facilities.edit', [
            'isSet' => false,
            'facility' => $facility,
            'facilitySet' => null,
            'individualFacilities' => $individualFacilities,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'facility_type' => ['required', 'in:single,set'],
            'name' => ['required', 'string', 'max:255'],
            'prefix_code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'daily_rate' => ['nullable', 'numeric', 'min:0'],
            'rate_type' => ['required', 'in:hourly,daily,both'],
            'is_active' => ['boolean'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'image_paths' => ['nullable', 'string'],
            'member_facilities' => ['nullable', 'array'],
            'member_facilities.*' => ['integer', 'exists:facilities,facility_id'],
        ]);

        $isSet = ($validated['facility_type'] === 'set');

        [$rate, $rateType, $hourlyRate, $dailyRate] = $this->resolveRates($validated);

        if ($isSet) {
            $set = FacilitySet::findOrFail($id);

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

            return redirect()->route('frontdesk.facilities.index')
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

        return redirect()->route('frontdesk.facilities.index')
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

            return redirect()->route('frontdesk.facilities.index')
                ->with('success', 'Facility set "'.$set->name.'" has been '.$label.'.');
        }

        $facility = Facility::findOrFail($id);
        $facility->update(['is_active' => ! $facility->is_active]);
        Cache::forget('public_showcase_data');
        ActivityLog::log('FACILITY_TOGGLED', "Toggled facility {$facility->name} active status.");
        $label = $facility->is_active ? 'enabled' : 'disabled';

        return redirect()->route('frontdesk.facilities.index')
            ->with('success', 'Facility "'.$facility->name.'" has been '.$label.'.');
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        return redirect()->route('frontdesk.facilities.index')
            ->with('error', 'Deletion is disabled. Use the Enable/Disable toggle instead.');
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    /**
     * @return array{float, string, float|null, float|null}
     */
    private function resolveRates(array $validated): array
    {
        $rateType = $validated['rate_type'];
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
