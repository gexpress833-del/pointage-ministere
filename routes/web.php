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

Route::get('/debug', function () {
    $checks = [];

    // APP_KEY
    $key = config('app.key');
    $checks['app_key'] = [
        'set' => !empty($key),
        'starts_with_base64' => str_starts_with($key ?? '', 'base64:'),
        'length' => strlen($key ?? ''),
    ];

    // Database
    try {
        \DB::select('SELECT 1');
        $checks['database'] = 'OK';
    } catch (\Exception $e) {
        $checks['database'] = 'ERROR: ' . $e->getMessage();
    }

    // Sessions table
    try {
        \DB::select('SELECT 1 FROM sessions LIMIT 1');
        $checks['sessions_table'] = 'OK';
    } catch (\Exception $e) {
        $checks['sessions_table'] = 'ERROR: ' . $e->getMessage();
    }

    // Cache table
    try {
        \DB::select('SELECT 1 FROM cache LIMIT 1');
        $checks['cache_table'] = 'OK';
    } catch (\Exception $e) {
        $checks['cache_table'] = 'ERROR: ' . $e->getMessage();
    }

    // Users table
    try {
        \DB::select('SELECT 1 FROM users LIMIT 1');
        $checks['users_table'] = 'OK';
    } catch (\Exception $e) {
        $checks['users_table'] = 'ERROR: ' . $e->getMessage();
    }

    // Bureaux table
    try {
        \DB::select('SELECT 1 FROM bureaux LIMIT 1');
        $checks['bureaux_table'] = 'OK';
    } catch (\Exception $e) {
        $checks['bureaux_table'] = 'ERROR: ' . $e->getMessage();
    }

    // All tables
    try {
        $tables = \DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
        $checks['all_tables'] = array_map(fn($t) => $t->tablename, $tables);
    } catch (\Exception $e) {
        $checks['all_tables'] = 'ERROR: ' . $e->getMessage();
    }

    // Users count
    try {
        $checks['users_count'] = \DB::table('users')->count();
        $checks['users_list'] = \DB::table('users')->select('id', 'email', 'role')->get();
    } catch (\Exception $e) {
        $checks['users_count'] = 'ERROR: ' . $e->getMessage();
    }

    // Bureaux count
    try {
        $checks['bureaux_count'] = \DB::table('bureaux')->count();
    } catch (\Exception $e) {
        $checks['bureaux_count'] = 'ERROR: ' . $e->getMessage();
    }

    // Vite manifest
    $checks['vite_manifest'] = file_exists(public_path('build/manifest.json')) ? 'OK' : 'MISSING';

    // Storage dirs
    $checks['storage_framework'] = is_writable(storage_path('framework')) ? 'writable' : 'NOT writable';
    $checks['storage_logs'] = is_writable(storage_path('logs')) ? 'writable' : 'NOT writable';

    // View cache
    $checks['view_cache_dir'] = is_writable(storage_path('framework/views')) ? 'writable' : 'NOT writable';

    // Landing view exists
    $checks['landing_view'] = view()->exists('landing') ? 'OK' : 'MISSING';

    // Institutional header component
    $checks['institutional_header'] = view()->exists('components.official-institutional-header') ? 'OK' : 'MISSING';

    // Env vars
    $checks['env'] = [
        'APP_ENV' => env('APP_ENV'),
        'APP_DEBUG' => env('APP_DEBUG'),
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'SESSION_DRIVER' => env('SESSION_DRIVER'),
        'CACHE_STORE' => env('CACHE_STORE'),
    ];

    return response()->json($checks, 200, [], JSON_PRETTY_PRINT);
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
