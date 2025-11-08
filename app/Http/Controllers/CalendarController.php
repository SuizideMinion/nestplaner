<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $family = Auth::user()->families()->first();
        return view('calendar.index', compact('family'));
    }

    public function fetch()
    {
        $familyId = session('active_family_id');
        $events = collect();

        // Normale Termine
        $normalEvents = CalendarEvent::where('family_id', $familyId)
            ->where('is_recurring', false)
            ->get();

        // Wiederkehrende Termine
        $recurringEvents = CalendarEvent::where('family_id', $familyId)
            ->where('is_recurring', true)
            ->get();

        // Globale Feiertage
        $globalHolidays = CalendarEvent::whereNull('family_id')
            ->where('is_recurring', true)
            ->get();

        $today = Carbon::today();
        $startOfYear = $today->copy()->startOfYear();
        $endOfYear = $today->copy()->endOfYear();

        foreach ($recurringEvents as $event) {
            $base = Carbon::parse($event->start);
            $until = $event->recurrence_date
                ? Carbon::parse($event->recurrence_date)
                : $endOfYear;

            // Maximal 365 Wiederholungen pro Event (Performance-Schutz)
            while ($base->lessThanOrEqualTo($until)) {
                $events->push([
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $base->toDateTimeString(),
                    'color' => $event->color,
                    'allDay' => true,
                ]);

                switch ($event->recurrence_type) {
                    case 'daily':
                        $base->addDay();
                        break;
                    case 'weekly':
                        $base->addWeek();
                        break;
                    case 'monthly':
                        $base->addMonth();
                        break;
                    case 'yearly':
                        $base->addYear();
                        break;
                    default:
                        break 2; // kein wiederkehrender Typ -> raus
                }
            }
        }

        // Normale + wiederkehrende + globale Events zusammenführen
        $merged = $normalEvents->map(function ($e) {
            return [
                'id' => $e->id,
                'title' => $e->title,
                'start' => $e->start,
                'end' => $e->end,
                'color' => $e->color,
                'allDay' => false,
            ];
        })->concat($events)->concat(
            $globalHolidays->map(function ($h) {
                return [
                    'id' => "holiday-{$h->id}",
                    'title' => $h->title . ' 🕊️',
                    'start' => Carbon::parse($h->recurrence_date)->year(now()->year)->toDateString(),
                    'color' => '#ef4444',
                    'allDay' => true,
                ];
            })
        );

        return response()->json($merged->values());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $activeFamilyId = session('active_family_id');

        if (!$activeFamilyId || !$user->families()->where('families.id', $activeFamilyId)->exists()) {
            return response()->json(['error' => 'Keine aktive Familie ausgewählt oder Zugriff verweigert.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end'   => 'nullable|date|after_or_equal:start',
            'color' => 'nullable|string|max:20',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'nullable|string|in:daily,weekly,monthly,yearly',
            'recurrence_date' => 'nullable|date',
        ]);

        $validated['family_id'] = $activeFamilyId;
        $event = CalendarEvent::create($validated);

        return response()->json($event, 201);
    }


    public function update(Request $request, CalendarEvent $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'start' => 'sometimes|date',
            'end'   => 'nullable|date|after_or_equal:start',
            'color' => 'nullable|string|max:20',
        ]);

        $event->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy(CalendarEvent $event)
    {
        try {
            $event->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
