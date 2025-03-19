<?php

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        env('VERCEL_ENV') ? '/tmp/storage/framework/views' : realpath(storage_path('framework/views'))
    ),
];