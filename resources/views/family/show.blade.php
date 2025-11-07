<x-app-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Familie: {{ $family->name }}</h2>
            <a href="{{ route('family.qr', $family) }}" class="btn btn-outline-secondary">
                <i class="bi bi-qr-code"></i> Einladungscode anzeigen
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <h4>Mitglieder</h4>
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Rolle</th>
                <th>Beigetreten</th>
            </tr>
            </thead>
            <tbody>
            @foreach($family->users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>
                        <form action="{{ route('family.members.updateRole', [$family->id, $user->id]) }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            <select name="role" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                @foreach(['parent' => 'Elternteil', 'child' => 'Kind', 'guest' => 'Gast'] as $value => $label)
                                    <option value="{{ $value }}" {{ $user->pivot->role === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </td>

                    <td>{{ $user->pivot->created_at->format('d.m.Y H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
