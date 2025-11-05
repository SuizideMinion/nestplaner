<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasFamily
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Prüfen, ob User in mindestens einer Familie ist
        if ($user->families()->count() === 0) {
            return redirect()->route('families.create')
                ->with('error', 'Du musst zuerst einer Familie beitreten oder eine erstellen.');
        }

        // Prüfen, ob eine aktive Familie ausgewählt ist (Session)
        if (!session()->has('active_family_id')) {
            // Wenn mehrere Familien vorhanden → Auswahlseite oder erste Familie automatisch aktivieren
            $firstFamily = $user->families()->first();
            if ($firstFamily) {
                session(['active_family_id' => $firstFamily->id]);
            } else {
                return redirect()->route('families.create')
                    ->with('error', 'Bitte wähle oder erstelle eine Familie.');
            }
        }

        return $next($request);
    }
}
