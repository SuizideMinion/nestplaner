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

        $normalEvents = CalendarEvent::where('family_id', $familyId)
            ->where('is_recurring', false)
            ->get();

        $recurringEvents = CalendarEvent::where('family_id', $familyId)
            ->where('is_recurring', true)
            ->get();

        $globalHolidays = CalendarEvent::whereNull('family_id')
            ->where('is_recurring', true)
            ->get();

        $today = Carbon::today();
        $year = $today->year;

        // generiere virtuelle Kopien für wiederkehrende Events
        $expanded = $recurringEvents->map(function ($event) use ($year) {
            $base = Carbon::parse($event->recurrence_date);

            switch ($event->recurrence_type) {
                case 'yearly':
                    $start = $base->copy()->year($year);
                    break;
                case 'monthly':
                    $start = Carbon::create($year, now()->month, $base->day);
                    break;
                case 'weekly':
                    $start = now()->startOfWeek()->addDays($base->dayOfWeek);
                    break;
                default:
                    $start = $base;
            }

            return [
                'id' => $event->id,
                'title' => $event->title . ' 🎂',
                'start' => $start->toDateString(),
                'color' => $event->color,
                'allDay' => true,
            ];
        });

        return response()->json(
            $normalEvents
                ->concat($expanded)
                ->concat($globalHolidays->map(fn($h) => [
                    'id' => "holiday-{$h->id}",
                    'title' => $h->title . ' 🕊️',
                    'start' => Carbon::parse($h->recurrence_date)->year(now()->year)->toDateString(),
                    'color' => '#ef4444',
                    'allDay' => true,
                ]))
        );
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
