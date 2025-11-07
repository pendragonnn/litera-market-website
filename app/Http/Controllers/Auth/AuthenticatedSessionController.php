<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();

            $user = Auth::user();

            // dd($user);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Welcome back, Admin 👋');
            }

            return redirect()->route('home')
                ->with('success', 'Welcome back, ' . $user->name . '! Happy shopping 🛒');
        } catch (ValidationException $e) {
            // Ini error karena email/password salah
            return back()->with('error', 'Login failed! Please check your credentials.');
        } catch (\Throwable $e) {
            // Ini untuk error lain (misal DB error, dsb)
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'You have been logged out successfully 👋');
    }
}
