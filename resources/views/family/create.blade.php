<x-app-layout>
    <div class="container py-4">
        <h2>Neue Familie erstellen</h2>
        <form method="POST" action="{{ route('family.store') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Familienname</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Erstellen</button>
        </form>
    </div>
</x-app-layout>
