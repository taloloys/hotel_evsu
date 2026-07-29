<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LandingPageShowcase;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function index(): View
    {
        $activeRoomTypes = Room::where('is_active', true)
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type');

        foreach ($activeRoomTypes as $roomType) {
            LandingPageShowcase::firstOrCreate(
                ['type' => 'ROOM', 'title' => $roomType],
                [
                    'category' => str_replace(' Room', '', $roomType) ?: 'Standard',
                    'price_rate' => '',
                    'capacity' => '2 Guests',
                    'icon' => 'fa-bed',
                    'images' => [],
                    'is_active' => true,
                ]
            );
        }

        $rooms = LandingPageShowcase::where('type', 'ROOM')
            ->whereIn('title', $activeRoomTypes)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $cafeteriaMain = LandingPageShowcase::where('type', 'CAFETERIA_MAIN')->first();

        $cafeteriaItems = LandingPageShowcase::where('type', 'CAFETERIA_ITEM')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $roomTypes = $activeRoomTypes;
        $categories = $roomTypes->map(function ($t) {
            return str_replace(' Room', '', $t);
        })->unique()->values();

        return view('admin.landing-page.index', compact('rooms', 'cafeteriaMain', 'cafeteriaItems', 'roomTypes', 'categories'));
    }

    public function updateRoom(Request $request, LandingPageShowcase $showcase): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'price_rate' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'string', 'max:100'],
            'badge' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'image_paths' => ['nullable', 'string'],
        ]);

        // 1. Get initial image paths from text input if provided, or from current model values
        if (isset($validated['image_paths'])) {
            $imagePaths = array_filter(array_map('trim', explode(',', $validated['image_paths'])));
        } else {
            $imagePaths = $showcase->images ?? [];
        }

        // 2. Append any newly uploaded files
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                if (app()->environment('testing')) {
                    Storage::disk('public')->putFileAs('images/showcase/rooms', $file, $filename);
                } else {
                    $file->move(public_path('images/showcase/rooms'), $filename);
                }
                $imagePaths[] = 'images/showcase/rooms/'.$filename;
            }
        }

        $showcase->update([
            'category' => $validated['category'],
            'price_rate' => $validated['price_rate'] ?? '',
            'capacity' => $validated['capacity'],
            'badge' => $validated['badge'] ?? null,
            'icon' => $validated['icon'] ?? 'fa-bed',
            'images' => array_values(array_unique($imagePaths)),
        ]);

        Cache::forget('public_showcase_data');

        ActivityLog::log('LANDING_PAGE_MODIFIED', "Updated showcase room: {$showcase->title}.");

        return redirect()->route('admin.landing-page')->with('success', 'Showcase room updated successfully.');
    }

    public function updateCafeteriaMain(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'timing' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'image_path' => ['nullable', 'string'],
        ]);

        $main = LandingPageShowcase::firstOrNew(['type' => 'CAFETERIA_MAIN']);

        $imagePaths = $main->images ?? [];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'cafeteria_main_'.time().'.'.$file->getClientOriginalExtension();
            if (app()->environment('testing')) {
                Storage::disk('public')->putFileAs('images/showcase/coffeeshop', $file, $filename);
            } else {
                $file->move(public_path('images/showcase/coffeeshop'), $filename);
            }
            $imagePaths = ['images/showcase/coffeeshop/'.$filename];
        } elseif (! empty($validated['image_path'])) {
            $imagePaths = [$validated['image_path']];
        }

        if (empty($imagePaths)) {
            $imagePaths = ['images/showcase/coffeeshop/cafeteria_main.jpg'];
        }

        $main->fill([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'timing' => $validated['timing'],
            'images' => $imagePaths,
            'is_active' => true,
        ]);
        $main->save();

        Cache::forget('public_showcase_data');

        ActivityLog::log('LANDING_PAGE_MODIFIED', 'Updated main cafeteria showcase hero image and timing.');

        return redirect()->route('admin.landing-page')->with('success', 'Cafeteria main showcase updated.');
    }

    public function storeCafeteriaItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'timing' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'image_path' => ['nullable', 'string'],
        ]);

        $imagePath = 'images/showcase/coffeeshop/coffee.jpg';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            if (app()->environment('testing')) {
                Storage::disk('public')->putFileAs('images/showcase/coffeeshop', $file, $filename);
            } else {
                $file->move(public_path('images/showcase/coffeeshop'), $filename);
            }
            $imagePath = 'images/showcase/coffeeshop/'.$filename;
        } elseif (! empty($validated['image_path'])) {
            $imagePath = $validated['image_path'];
        }

        LandingPageShowcase::create([
            'type' => 'CAFETERIA_ITEM',
            'title' => $validated['title'],
            'category' => $validated['category'],
            'timing' => $validated['timing'],
            'icon' => $validated['icon'] ?? 'fa-mug-hot',
            'images' => [$imagePath],
            'is_active' => true,
        ]);

        Cache::forget('public_showcase_data');

        ActivityLog::log('LANDING_PAGE_MODIFIED', "Created cafeteria item: {$validated['title']}.");

        return redirect()->route('admin.landing-page')->with('success', 'Cafeteria item added successfully.');
    }

    public function toggleStatus(LandingPageShowcase $showcase): RedirectResponse
    {
        $showcase->update([
            'is_active' => ! $showcase->is_active,
        ]);

        Cache::forget('public_showcase_data');

        ActivityLog::log('LANDING_PAGE_MODIFIED', "Toggled showcase item {$showcase->title} active status.");

        return redirect()->route('admin.landing-page')->with('success', 'Showcase status updated.');
    }

    public function destroy(LandingPageShowcase $showcase): RedirectResponse
    {
        $title = $showcase->title;
        $showcase->delete();

        Cache::forget('public_showcase_data');

        ActivityLog::log('LANDING_PAGE_MODIFIED', "Deleted showcase item: {$title}.");

        return redirect()->route('admin.landing-page')->with('success', 'Showcase item deleted.');
    }
}
