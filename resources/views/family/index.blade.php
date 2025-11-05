<x-app-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Meine Familien</h2>
            <a href="{{ route('family.create') }}" class="btn btn-primary">+ Neue Familie</a>
        </div>

        @if($families->isEmpty())
            <div class="alert alert-info">Du bist aktuell in keiner Familie.</div>
        @else
            <div class="list-group">
                @foreach($families as $family)
                    <a href="{{ route('family.show', $family) }}" class="list-group-item list-group-item-action">
                        <strong>{{ $family->name }}</strong><br>
                        <small>Erstellt von: {{ $family->owner->name ?? 'Unbekannt' }}</small>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
