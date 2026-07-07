<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Livewire uses temporary file uploads for file upload features.
    | This configuration controls where temp files are stored and
    | for how long they are kept.
    |
    */

    'temporary_file_upload' => [
        'disk' => 'local',
        'rules' => null,
        'max_upload' => 10240, // 10MB in KB
        'max_upload_time' => 60, // seconds
        'cleanup' => true,
        'paths' => [
            'storage_path' => 'livewire-tmp/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Manifest File
    |--------------------------------------------------------------------------
    |
    | Livewire's manifest file caches the JavaScript snippets for components.
    |
    */

    'manifest_path' => storage_path('app/livewire-manifest.json'),

    /*
    |--------------------------------------------------------------------------
    | Back Button Cache
    |--------------------------------------------------------------------------
    |
    | This configuration controls whether Livewire should cache the page
    | state when the user navigates away and comes back using the
    | browser's back button.
    |
    */

    'back_button_cache' => false,

    /*
    |--------------------------------------------------------------------------
    | Render On Redirect
    |--------------------------------------------------------------------------
    |
    | This configuration controls whether Livewire should render the
    | component before redirecting.
    |
    */

    'render_on_redirect' => false,
];
