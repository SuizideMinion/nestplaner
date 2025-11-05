<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🎉 Wiederkehrende Ereignisse
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Formular -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <form method="POST" action="{{ route('recurring.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Titel</label>
                            <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="z. B. Klara Geburtstag" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Datum</label>
                            <input type="date" name="recurrence_date" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Typ</label>
                            <select name="recurrence_type" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="yearly">Jährlich</option>
                                <option value="monthly">Monatlich</option>
                                <option value="weekly">Wöchentlich</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Farbe</label>
                            <input type="color" name="color" value="#007bff" class="w-full h-10 border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="flex items-end">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md w-full">+</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabelle -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                @if($events->isEmpty())
                    <p class="text-gray-500 text-center">Keine wiederkehrenden Ereignisse vorhanden.</p>
                @else
                    <table class="min-w-full text-sm text-left border border-gray-200 rounded-lg">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border-b">Titel</th>
                            <th class="px-4 py-2 border-b">Datum</th>
                            <th class="px-4 py-2 border-b">Typ</th>
                            <th class="px-4 py-2 border-b">Farbe</th>
                            <th class="px-4 py-2 border-b text-right">Aktion</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($events as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border-b">{{ $event->title }}</td>
                                <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($event->recurrence_date)->format('d.m.Y') }}</td>
                                <td class="px-4 py-2 border-b capitalize">{{ $event->recurrence_type }}</td>
                                <td class="px-4 py-2 border-b">
                                    <span class="inline-block w-5 h-5 rounded-md border" style="background-color: {{ $event->color }}"></span>
                                </td>
                                <td class="px-4 py-2 border-b text-right">
                                    <form method="POST" action="{{ route('recurring.destroy', $event->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs" onclick="return confirm('Wirklich löschen?')">Löschen</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
