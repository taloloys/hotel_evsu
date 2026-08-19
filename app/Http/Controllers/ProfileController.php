<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(): View
    {
        $user = auth()->user();

        return view('profile.show', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $user->update([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
        ]);

        ActivityLog::log(
            'PROFILE_UPDATED',
            "User {$user->username} updated their profile information."
        );

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile information updated successfully.');
    }
}
