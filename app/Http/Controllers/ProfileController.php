<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password_hash)) {
            return redirect()
                ->route('profile.show')
                ->withErrors(['current_password' => 'The provided current password does not match our records.']);
        }

        $user->update([
            'password_hash' => Hash::make($validated['password']),
        ]);

        ActivityLog::log(
            'PASSWORD_CHANGED',
            "User {$user->username} updated their account password."
        );

        return redirect()
            ->route('profile.show')
            ->with('success', 'Password updated successfully.');
    }
}
