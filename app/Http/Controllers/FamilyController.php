<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // 👈 hinzufügen

class FamilyController extends Controller
{
    use AuthorizesRequests; // 👈 Trait aktivieren

    public function index()
    {
        $families = Auth::user()->families()->with('owner')->get();
        return view('family.index', compact('families'));
    }

    public function create()
    {
        return view('family.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $family = Family::create([
            'name' => $request->name,
            'owner_id' => Auth::id(),
            'invite_code' => Str::upper(Str::random(8)),
        ]);

        $family->users()->attach(Auth::id(), ['role' => 'parent']);

        return redirect()->route('family.show', $family)
            ->with('success', 'Familie erfolgreich erstellt!');
    }

    public function show(Family $family)
    {
        $this->authorize('view', $family);
        $family->load('users');
        return view('family.show', compact('family'));
    }

    public function join($code)
    {
        $family = Family::where('invite_code', $code)->firstOrFail();

        if (!Auth::check()) {
            // Einladungscode in Session speichern und zur Registrierung schicken
            session(['invite_code' => $code]);
            return redirect()->route('register')->with('info', 'Bitte registriere dich, um der Familie beizutreten.');
        }

        // Wenn eingeloggt → direkt beitreten
        $family->users()->syncWithoutDetaching([Auth::id() => ['role' => 'guest']]);

        return redirect()->route('family.show', $family)
            ->with('success', 'Du bist der Familie beigetreten!');
    }

    public function qr(Family $family)
    {
        $this->authorize('view', $family);

        $url = route('family.join', $family->invite_code);
        $qr = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($url);


        return view('family.qr', compact('family', 'qr', 'url'));
    }

}
