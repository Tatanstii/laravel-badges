<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image storage disk
    |--------------------------------------------------------------------------
    |
    | Filesystem disk used to resolve badge image URLs. Set this to any disk
    | configured in your application's config/filesystems.php.
    |
    */
    'disk' => env('BADGES_DISK', 'public'),
];
