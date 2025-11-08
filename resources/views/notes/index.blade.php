<x-app-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Tagebuch & Notizen</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                <i class="bi bi-plus-circle"></i> Neue Notiz
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($notes->isEmpty())
            <div class="alert alert-info">Noch keine Notizen vorhanden.</div>
        @else
            <div class="row">
                @foreach($notes as $note)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><a href="{{ route('notes.show', $note->id) }}"> {{ $note->title }}</a></h5>
                                <p class="card-text text-muted small mb-2">
                                    {{ $note->user->name }} – {{ $note->created_at->format('d.m.Y H:i') }}
                                </p>
                                <p class="card-text">{{ Str::limit($note->content, 150) }}</p>
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-{{ $note->visibility == 'private' ? 'secondary' : ($note->visibility == 'parents' ? 'warning' : 'success') }}">
                                        {{ ucfirst($note->visibility) }}
                                    </span>
                                    <form method="POST" action="{{ route('notes.destroy', $note) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Notiz löschen?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal: Neue Notiz -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('notes.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNoteModalLabel">Neue Notiz hinzufügen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="noteTitle" class="form-label">Titel</label>
                            <input type="text" name="title" id="noteTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="noteContent" class="form-label">Inhalt</label>
                            <textarea name="content" id="noteContent" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="noteVisibility" class="form-label">Sichtbarkeit</label>
                            <select name="visibility" id="noteVisibility" class="form-select">
                                <option value="family">Familie</option>
                                <option value="parents">Nur Eltern</option>
                                <option value="private">Privat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-success">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
