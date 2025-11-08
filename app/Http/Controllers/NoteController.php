<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    public function index()
    {
        $activeFamilyId = session('active_family_id');
        $user = Auth::user();

        // Prüfen, ob der Nutzer Elternrolle in dieser Familie hat
        $isParent = $user->families()
            ->where('families.id', $activeFamilyId)
            ->wherePivot('role', 'parent')
            ->exists();

        $notes = Note::where('family_id', $activeFamilyId)
            ->where(function ($query) use ($user, $isParent) {
                $query->where('visibility', 'family');

                if ($isParent) {
                    $query->orWhere('visibility', 'parents');
                }

                $query->orWhere(function ($q) use ($user) {
                    $q->where('visibility', 'private')
                        ->where('user_id', $user->id);
                });
            })
            ->latest()
            ->get();

        return view('notes.index', compact('notes'));
    }

    public function show(Note $note)
    {
        $user = Auth::user();

        // Sichtbarkeitsprüfung
        if ($note->visibility === 'private' && $note->user_id !== $user->id) {
            abort(403);
        }

        if ($note->visibility === 'parents') {
            $isParent = $user->families()
                ->where('families.id', $note->family_id)
                ->wherePivot('role', 'parent')
                ->exists();

            if (!$isParent && $note->user_id !== $user->id) {
                abort(403);
            }
        }

        $note->load(['comments.user']);
        return view('notes.show', compact('note'));
    }

    public function addComment(Request $request, $id)
    {
        $note = Note::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $note->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return redirect()->route('notes.show', $note)->with('success', 'Kommentar hinzugefügt.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'visibility' => 'required|string|in:family,parents,private',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['family_id'] = session('active_family_id');
        $validated['is_private'] = $validated['visibility'] === 'private';

        Note::create($validated);

        return redirect()->route('notes.index')->with('success', 'Notiz gespeichert.');
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        $note->delete();

        return back()->with('success', 'Notiz gelöscht.');
    }
}
