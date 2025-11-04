<?php

use App\Http\Controllers\ProfileController;
use App\Notifications\TestPush;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/notify/test', function () {
    $user = Auth::user();
    if (!$user) return redirect('/login');
    $user->notify(new TestPush());
    return 'Push gesendet!';
});

Route::post('/webpush/subscribe', function (Request $request) {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $user->updatePushSubscription(
        $request->input('endpoint'),
        $request->input('keys.p256dh'),
        $request->input('keys.auth')
    );

    return response()->json(['success' => true]);
})->name('webpush.subscribe');

Route::get('/notify/test', function () {
    $user = Auth::user();
    if (!$user) {
        return redirect('/login');
    }

    $user->notify(new TestPush());
    return 'Push gesendet!';
});

require __DIR__.'/auth.php';
