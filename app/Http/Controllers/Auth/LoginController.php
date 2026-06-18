<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('welcome');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'username' => 'The provided credentials are incorrect.',
                ])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->dashboardRouteForRole(Auth::user()?->role?->role_name));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function dashboardRouteForRole(?string $role): string
    {
        return match ($role) {
            'ADMIN' => route('admin.dashboard'),
            'FRONT_DESK' => route('frontdesk.dashboard'),
            'ACCOUNTING' => route('accounting.dashboard'),
            'CAFETERIA' => route('coffeeshop.dashboard'),
            default => route('home'),
        };
    }
}
