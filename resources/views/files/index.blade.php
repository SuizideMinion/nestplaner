<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4">📁 Familien-Dateien</h2>

        <!-- Neuen Ordner erstellen -->
        <form method="POST" action="{{ route('files.storeFolder') }}" class="row g-2 mb-4">
            @csrf
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Ordnername" required>
            </div>
            <div class="col-md-3">
                <select name="visibility" class="form-select">
                    <option value="all">Alle Familienmitglieder</option>
                    <option value="parents">Nur Eltern</option>
                    <option value="owner">Nur ich</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100"><i class="bi bi-folder-plus"></i> Erstellen</button>
            </div>
        </form>

        @foreach($folders as $folder)
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $folder->name }} <small class="text-muted">({{ $folder->visibility }})</small></h5>
                    <form action="{{ route('files.upload', $folder) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                        @csrf
                        <input type="file" name="files[]" class="form-control form-control-sm" multiple required>
                        <button class="btn btn-primary btn-sm"><i class="bi bi-upload"></i></button>
                    </form>
                </div>
                <div class="card-body">
                    @if($folder->files->isEmpty())
                        <p class="text-muted">Keine Dateien vorhanden.</p>
                    @else
                        <ul class="list-group">
                            @foreach($folder->files as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-file-earmark-text"></i> {{ $file->original_name }}
                                        <small class="text-muted">({{ number_format($file->size / 1024, 1) }} KB)</small>
                                    </div>
                                    <div>
                                        <button
                                            class="btn btn-sm btn-outline-info btn-preview"
                                            data-url="{{ Storage::url('public/family_files/'.$file->filename) }}"
                                            data-type="{{ $file->mime_type }}"
                                            title="Vorschau"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>

                                        <form action="{{ route('files.destroy', $file) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Löschen">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <!-- Vorschau-Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dateivorschau</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="filePreviewContent">
                    <p class="text-muted">Lade Vorschau...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
            const modalBody = document.getElementById('filePreviewContent');

            document.querySelectorAll(".btn-preview").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.preventDefault();
                    const url = e.currentTarget.getAttribute("data-url");
                    const type = e.currentTarget.getAttribute("data-type");

                    modalBody.innerHTML = '<p class="text-muted">Lade Vorschau...</p>';

                    if (type.startsWith("image/")) {
                        modalBody.innerHTML = `<img src="${url}" class="img-fluid rounded shadow-sm" alt="Vorschau">`;
                    } else if (type === "application/pdf") {
                        modalBody.innerHTML = `<iframe src="${url}" width="100%" height="700" class="border-0"></iframe>`;
                    } else {
                        modalBody.innerHTML = `<p class="text-muted">Keine Vorschau verfügbar.<br><a href="${url}" target="_blank">Datei öffnen</a></p>`;
                    }

                    modal.show();
                });
            });
        });
    </script>

</x-app-layout>
