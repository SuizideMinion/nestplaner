<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = CalendarEvent::whereNull('family_id')
            ->where('is_recurring', true)
            ->whereNotNull('recurrence_type')
            ->orderBy('recurrence_date')
            ->get();

        return view('admin.holidays.index', compact('holidays'));
    }

    public function import(Request $request)
    {
        $year = $request->input('year', now()->year);
        $response = Http::get("https://date.nager.at/api/v3/PublicHolidays/{$year}/DE");

        if ($response->failed()) {
            return back()->with('error', 'Fehler beim Abrufen der Feiertage von der API.');
        }

        $count = 0;
        foreach ($response->json() as $holiday) {
            CalendarEvent::updateOrCreate([
                'title' => $holiday['localName'],
                'recurrence_date' => $holiday['date'],
                'recurrence_type' => 'yearly',
                'is_recurring' => true,
                'family_id' => null, // global für alle
            ], [
                'color' => '#ef4444',
                'start' => $holiday['date'],
            ]);
            $count++;
        }

        return back()->with('success', "{$count} Feiertage für {$year} importiert.");
    }
}
