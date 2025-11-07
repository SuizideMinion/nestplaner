<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActiveFamilyController extends Controller
{
    public function switch(Request $request, $familyId)
    {
        $user = Auth::user();

        if (!$user->families()->where('families.id', $familyId)->exists()) {
            abort(403, 'Du gehörst nicht zu dieser Familie.');
        }

        session(['active_family_id' => $familyId]);

        $user = auth()->user();
        $user->update(['last_family_id' => $familyId]);

        return back()->with('success', 'Aktive Familie gewechselt.');
    }
}
