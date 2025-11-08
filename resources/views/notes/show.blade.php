<x-app-layout>
    <div class="container py-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h3>{{ $note->title }}</h3>
                <p class="text-muted small">
                    Erstellt am {{ $note->created_at->format('d.m.Y H:i') }} von {{ $note->user->name }}
                </p>
                <hr>
                <div class="mt-3" style="white-space: pre-wrap;">
                    {!! nl2br(e($note->content)) !!}
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Kommentare</h5>
            </div>
            <div class="card-body">
                @forelse($note->comments as $comment)
                    <div class="mb-3 border-bottom pb-2">
                        <strong>{{ $comment->user->name }}</strong>
                        <small class="text-muted">· {{ $comment->created_at->diffForHumans() }}</small>
                        <p class="mb-1 mt-1">{{ $comment->content }}</p>
                    </div>
                @empty
                    <p class="text-muted">Noch keine Kommentare vorhanden.</p>
                @endforelse

                <form action="{{ route('notes.comment', $note->id) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="3" placeholder="Kommentar schreiben..." required></textarea>
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-send"></i> Abschicken</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
