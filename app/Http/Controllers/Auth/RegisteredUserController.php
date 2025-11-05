<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 🔗 Prüfen, ob ein Einladungscode in der Session ist
        if (session()->has('invite_code')) {
            $code = session('invite_code');
            $family = Family::where('invite_code', $code)->first();

            if ($family) {
                $family->users()->syncWithoutDetaching([$user->id => ['role' => 'guest']]);
            }

            session()->forget('invite_code');
            return redirect()->route('family.show', $family)
                ->with('success', 'Du wurdest erfolgreich registriert und der Familie beigetreten!');
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
