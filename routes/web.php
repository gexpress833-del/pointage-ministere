<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserPhotoController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'Laravel is working!';
});

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/changer-mot-de-passe', [PasswordChangeController::class, 'showChangeForm'])->name('password.change');
    Route::post('/changer-mot-de-passe', [PasswordChangeController::class, 'change'])->name('password.change.submit');
});

Route::middleware(['auth', 'password.force'])->prefix('presence')->name('presence.')->group(function () {
    Route::get('/', [PresenceController::class, 'dashboard'])->name('dashboard');
    Route::get('/sign', [PresenceController::class, 'showSign'])->name('sign');
    Route::post('/sign', [PresenceController::class, 'sign'])->name('sign.submit');
    Route::post('/sign-depart', [PresenceController::class, 'signDepart'])->name('sign-depart.submit');
    Route::get('/reference-photo', [PresenceController::class, 'referencePhoto'])->name('reference-photo');
    Route::get('/historique', [PresenceController::class, 'historique'])->name('historique');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'password.force'])->group(function () {
    Route::get('/reports/daily/{session}', [ReportController::class, 'dailyPdf'])->name('reports.daily');
    Route::get('/reports/monthly', [ReportController::class, 'monthlyPdf'])->name('reports.monthly');
    Route::get('/users/{user}/photo-reference', [UserPhotoController::class, 'show'])->name('users.photo-reference');
});
