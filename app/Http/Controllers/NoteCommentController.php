<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\NoteComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteCommentController extends Controller
{
    public function store(Request $request, Note $note)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        NoteComment::create([
            'note_id' => $note->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Kommentar hinzugefügt.');
    }

    public function destroy(NoteComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', 'Kommentar gelöscht.');
    }
}
