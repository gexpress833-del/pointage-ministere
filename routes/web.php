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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

Route::get('/test', function () {
    return 'Laravel is working!';
});

Route::get('/debug-upload', function () {
    $results = [];

    // 1. PHP info
    $results['php_version'] = PHP_VERSION;
    $results['max_file_uploads'] = ini_get('max_file_uploads');
    $results['upload_max_filesize'] = ini_get('upload_max_filesize');
    $results['post_max_size'] = ini_get('post_max_size');
    $results['tmp_dir'] = sys_get_temp_dir();
    $results['tmp_dir_writable'] = is_writable(sys_get_temp_dir());

    // 2. Storage directories
    $dirs = [
        'storage/app' => storage_path('app'),
        'storage/app/private' => storage_path('app/private'),
        'storage/app/private/photos_reference' => storage_path('app/private/photos_reference'),
        'storage/app/livewire-tmp' => storage_path('app/livewire-tmp'),
        'storage/app/public' => storage_path('app/public'),
        'storage/framework/sessions' => storage_path('framework/sessions'),
        'storage/framework/views' => storage_path('framework/views'),
        'storage/framework/cache' => storage_path('framework/cache'),
        'storage/logs' => storage_path('logs'),
    ];

    $results['directories'] = [];
    foreach ($dirs as $name => $path) {
        $results['directories'][$name] = [
            'path' => $path,
            'exists' => file_exists($path),
            'is_dir' => is_dir($path),
            'writable' => is_writable($path),
        ];
        if (! file_exists($path)) {
            @mkdir($path, 0775, true);
            $results['directories'][$name]['created_now'] = file_exists($path);
        }
    }

    // 3. Disk config
    $results['default_disk'] = config('filesystems.default');
    $results['local_disk_root'] = config('filesystems.disks.local.root');
    $results['local_disk_exists'] = is_dir(config('filesystems.disks.local.root'));

    // 4. Livewire config
    $results['livewire_temp_disk'] = config('livewire.temporary_file_upload.disk');
    $results['livewire_temp_path'] = config('livewire.temporary_file_upload.paths.storage_path');
    $results['livewire_manifest_path'] = config('livewire.manifest_path');

    // 5. Test write to local disk
    try {
        $disk = Storage::disk('local');
        $testContent = 'test_' . time();
        $disk->put('_debug_test.txt', $testContent);
        $readBack = $disk->get('_debug_test.txt');
        $results['local_disk_write_test'] = ($readBack === $testContent) ? 'OK' : 'READ_MISMATCH';
        $disk->delete('_debug_test.txt');
    } catch (\Exception $e) {
        $results['local_disk_write_test'] = 'ERROR: ' . $e->getMessage();
    }

    // 6. Test write to livewire-tmp
    try {
        $tmpPath = storage_path('app/livewire-tmp');
        if (! is_dir($tmpPath)) {
            mkdir($tmpPath, 0775, true);
        }
        $testFile = $tmpPath . '/_debug_test_' . time() . '.txt';
        file_put_contents($testFile, 'test');
        $results['livewire_tmp_write_test'] = file_exists($testFile) ? 'OK' : 'FAILED';
        @unlink($testFile);
    } catch (\Exception $e) {
        $results['livewire_tmp_write_test'] = 'ERROR: ' . $e->getMessage();
    }

    // 7. Imagekit config
    $results['imagekit_public_key_set'] = ! empty(config('imagekit.public_key'));
    $results['imagekit_private_key_set'] = ! empty(config('imagekit.private_key'));
    $results['imagekit_url_endpoint'] = config('imagekit.url_endpoint');
    $results['imagekit_upload_endpoint'] = config('imagekit.upload_endpoint');

    // 8. Test Imagekit connection (simple HTTP check)
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(10)
            ->get(config('imagekit.url_endpoint'));
        $results['imagekit_endpoint_reachable'] = $response->successful() ? 'OK (' . $response->status() . ')' : 'HTTP ' . $response->status();
    } catch (\Exception $e) {
        $results['imagekit_endpoint_reachable'] = 'ERROR: ' . $e->getMessage();
    }

    // 9. Test actual Imagekit auth
    try {
        $response = \Illuminate\Support\Facades\Http::withBasicAuth(config('imagekit.private_key'), '')
            ->timeout(10)
            ->get('https://api.imagekit.io/api/v1/files');
        $results['imagekit_auth_test'] = $response->successful() ? 'OK (' . $response->status() . ')' : 'HTTP ' . $response->status() . ' - ' . substr($response->body(), 0, 200);
    } catch (\Exception $e) {
        $results['imagekit_auth_test'] = 'ERROR: ' . $e->getMessage();
    }

    // 10. Simulate a file upload (like Livewire does)
    try {
        $fakeImage = storage_path('app/livewire-tmp/_debug_fake_upload_' . time() . '.jpg');
        // Create a minimal valid JPEG (1x1 pixel)
        $minJpeg = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwA/9k=');
        file_put_contents($fakeImage, $minJpeg);
        $results['simulate_upload_write'] = file_exists($fakeImage) ? 'OK' : 'FAILED';

        // Try to upload to Imagekit
        if (! empty(config('imagekit.private_key'))) {
            $imagekit = app(\App\Services\ImagekitService::class);
            $result = $imagekit->upload($fakeImage, 'debug_test_' . time() . '.jpg', '/photos_reference');
            $results['imagekit_upload_test'] = 'OK - URL: ' . ($result['url'] ?? 'NO_URL');
        } else {
            $results['imagekit_upload_test'] = 'SKIPPED - no private key set';
        }

        @unlink($fakeImage);
    } catch (\Exception $e) {
        $results['simulate_upload_write'] = 'ERROR: ' . $e->getMessage();
        $results['imagekit_upload_test'] = 'ERROR: ' . $e->getMessage();
        @unlink($fakeImage ?? '');
    }

    // 11. Current user info
    $results['authenticated'] = Auth::check();
    if (Auth::check()) {
        $u = Auth::user();
        $results['user_id'] = $u->id;
        $results['user_email'] = $u->email;
        $results['user_role'] = $u->role;
        $results['user_has_photo'] = ! empty($u->photo_reference);
        $results['user_photo_value'] = $u->photo_reference ? substr($u->photo_reference, 0, 80) : null;
    }

    // 12. Laravel logs (last 10 lines)
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $results['recent_logs'] = array_slice(file($logFile, FILE_IGNORE_NEW_LINES), -15);
    } else {
        $results['recent_logs'] = 'No log file';
    }

    return response()->json($results, 200, ['Content-Type' => 'application/json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
