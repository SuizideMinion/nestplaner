<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Family;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Die angegebenen Zugangsdaten sind ungültig.',
            ]);
        }

        $request->session()->regenerate();

        // 🔗 Prüfen, ob ein Einladungscode existiert
        if (session()->has('invite_code')) {
            $code = session('invite_code');
            $family = Family::where('invite_code', $code)->first();

            if ($family) {
                $family->users()->syncWithoutDetaching([Auth::id() => ['role' => 'guest']]);
            }

            session()->forget('invite_code');
            return redirect()->route('family.show', $family)
                ->with('success', 'Du bist der Familie beigetreten!');
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
