<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login'); // Points to resources/views/auth/login.blade.php
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'), // Uses lang/en/auth.php for messages
            ]);
        }

        $request->session()->regenerate();

        // Redirect based on role
        $user = Auth::user();
        if ($user->isAdmin()) {
            // Assuming you'll have an admin dashboard route like 'admin.dashboard'
            // If 'admin.dashboard' redirects further based on admin sub-roles, that's fine.
            return redirect()->intended(route('admin.dashboard', [], false));
        } elseif ($user->isProvider()) {
            // Redirect provider to their main requests page
            return redirect()->intended(route('provider.requests.index', [], false)); // MODIFIED LINE
        }

        // Default redirect for clients
        // The general 'dashboard' route in web.php will redirect clients to 'client.requests.my'
        return redirect()->intended(route('dashboard', [], false));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
