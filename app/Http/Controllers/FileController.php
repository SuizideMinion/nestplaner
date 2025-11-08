<?php

namespace App\Http\Controllers;

use App\Models\FileFolder;
use App\Models\FamilyFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $familyId = session('active_family_id');
        $folders = FileFolder::where('family_id', $familyId)->with('files')->get();

        return view('files.index', compact('folders'));
    }

    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'visibility' => 'required|in:all,parents,owner',
        ]);

        FileFolder::create([
            'family_id' => session('active_family_id'),
            'user_id' => Auth::id(),
            'name' => $request->name,
            'visibility' => $request->visibility,
        ]);

        return redirect()->back()->with('success', 'Ordner erstellt.');
    }

    public function upload(Request $request, FileFolder $folder)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:20480', // max 20 MB pro Datei
        ]);

        foreach ($request->file('files') as $file) {
            $path = $file->store('public/family_files');

            $folder->files()->create([
                'user_id' => Auth::id(),
                'filename' => basename($path),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return redirect()->back()->with('success', 'Dateien hochgeladen.');
    }


    public function download(FamilyFile $file)
    {
        return Storage::download('public/family_files/'.$file->filename, $file->original_name);
    }

    public function destroy(FamilyFile $file)
    {
        Storage::delete('public/family_files/'.$file->filename);
        $file->delete();

        return back()->with('success', 'Datei gelöscht.');
    }
}
