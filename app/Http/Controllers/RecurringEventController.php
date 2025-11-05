<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CalendarEvent;

class RecurringEventController extends Controller
{
    public function index()
    {
        $activeFamilyId = session('active_family_id');

        if (!$activeFamilyId) {
            return redirect()->route('family.index')
                ->with('error', 'Bitte wähle zuerst eine aktive Familie aus.');
        }

        $events = CalendarEvent::where('family_id', $activeFamilyId)
            ->where('is_recurring', true)
            ->orderBy('recurrence_date')
            ->get();

        return view('recurring.index', compact('events'));
    }

    public function store(Request $request)
    {
        $activeFamilyId = session('active_family_id');
        if (!$activeFamilyId) {
            return back()->with('error', 'Keine aktive Familie ausgewählt.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'recurrence_date' => 'required|date',
            'recurrence_type' => 'required|string|in:yearly,monthly,weekly',
            'color' => 'nullable|string|max:20',
        ]);

        CalendarEvent::create([
            'family_id' => $activeFamilyId,
            'title' => $validated['title'],
            'start' => $validated['recurrence_date'],
            'is_recurring' => true,
            'recurrence_type' => $validated['recurrence_type'],
            'recurrence_date' => $validated['recurrence_date'],
            'color' => $validated['color'] ?? '#007bff',
        ]);

        return redirect()->route('recurring.index')->with('success', 'Wiederkehrendes Ereignis hinzugefügt.');
    }

    public function destroy($id)
    {
        $event = CalendarEvent::findOrFail($id);
        $event->delete();

        return back()->with('success', 'Ereignis gelöscht.');
    }
}
