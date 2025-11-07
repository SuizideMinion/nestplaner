<?php

use App\Http\Controllers\ActiveFamilyController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecurringEventController;
use App\Models\User;
use App\Notifications\TestPush;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('holidays', [HolidayController::class, 'index'])->name('admin.holidays.index');
    Route::post('holidays/import', [HolidayController::class, 'import'])->name('admin.holidays.import');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/recurring-events', [RecurringEventController::class, 'index'])->name('recurring.index');
    Route::post('/recurring-events', [RecurringEventController::class, 'store'])->name('recurring.store');
    Route::delete('/recurring-events/{id}', [RecurringEventController::class, 'destroy'])->name('recurring.destroy');
});

Route::middleware(['auth', 'family'])->group(function () {
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'fetch'])->name('calendar.fetch');
    Route::post('/calendar/events', [CalendarController::class, 'store'])->name('calendar.store');
    Route::put('/calendar/events/{event}', [CalendarController::class, 'update'])->name('calendar.update');
    Route::delete('/calendar/events/{event}', [CalendarController::class, 'destroy'])->name('calendar.destroy');
});

Route::get('/join/{code}', [FamilyController::class, 'join'])->name('family.join');

Route::middleware(['auth'])->group(function () {
    Route::get('/families', [FamilyController::class, 'index'])->name('family.index');
    Route::get('/families/create', [FamilyController::class, 'create'])->name('family.create');
    Route::post('/families', [FamilyController::class, 'store'])->name('family.store');
    Route::get('/families/{family}', [FamilyController::class, 'show'])->name('family.show');
    Route::get('/families/{family}/qr', [FamilyController::class, 'qr'])->name('family.qr');
    Route::post('/families/{family}/members/{user}/role', [App\Http\Controllers\FamilyController::class, 'updateMemberRole'])
        ->name('family.members.updateRole');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/family/switch/{id}', [ActiveFamilyController::class, 'switch'])->name('family.switch');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/notify/test', function () {
    $user = User::first();
    if (!$user) return 'Kein Benutzer vorhanden';
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
