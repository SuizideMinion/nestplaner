<x-app-layout>
    <div class="container text-center py-4">
        <h2>QR-Code für {{ $family->name }}</h2>
        <div class="my-3">{!! $qr !!}</div> {{-- SVG direkt einbetten --}}
        <p>Scanne diesen Code oder öffne den Link:</p>
        <p><a href="{{ $url }}" class="btn btn-outline-primary">{{ $url }}</a></p>
        <p class="text-muted mt-3">
            Einladungscode: <strong>{{ $family->invite_code }}</strong>
        </p>
    </div>
</x-app-layout>
