<?php

// Set absolute paths for Vercel environment
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';
chdir($_SERVER['DOCUMENT_ROOT']);

// Ensure SQLite database exists before bootstrap
if (getenv('VERCEL_ENV') && !file_exists('/tmp/database.sqlite')) {
    touch('/tmp/database.sqlite');
    chmod('/tmp/database.sqlite', 0777);
}

// Forward Vercel requests to normal index.php
require __DIR__ . '/../public/index.php';