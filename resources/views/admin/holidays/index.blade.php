<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Feiertage verwalten</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 px-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.holidays.import') }}" class="flex items-center space-x-3 mb-6">
            @csrf
            <input type="number" name="year" class="border rounded px-3 py-2 w-24" value="{{ now()->year }}">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                Feiertage importieren
            </button>
        </form>

        <h3 class="text-lg font-bold mb-3">Importierte Feiertage</h3>

        @if($holidays->count())
            <table class="min-w-full bg-white border">
                <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">Datum</th>
                    <th class="px-4 py-2 border">Name</th>
                </tr>
                </thead>
                <tbody>
                @foreach($holidays as $holiday)
                    <tr>
                        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($holiday->recurrence_date)->format('d.m.Y') }}</td>
                        <td class="border px-4 py-2">{{ $holiday->title }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500">Noch keine Feiertage importiert.</p>
        @endif
    </div>
</x-app-layout>
