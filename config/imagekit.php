<?php

return [

    'public_key' => env('IMAGEKIT_PUBLIC_KEY', ''),

    'private_key' => env('IMAGEKIT_PRIVATE_KEY', ''),

    'url_endpoint' => env('IMAGEKIT_URL_ENDPOINT', 'https://ik.imagekit.io/your_default_url_endpoint'),

    'upload_endpoint' => 'https://upload.imagekit.io/api/v1/files/upload',

    'management_endpoint' => 'https://api.imagekit.io/api/v1/files',

];
