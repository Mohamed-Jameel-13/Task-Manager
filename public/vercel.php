<?php

// Enable error reporting for deployment debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database_path = '/tmp/database.sqlite';
$storage_path = '/tmp/storage';
$bootstrap_path = '/tmp/bootstrap';

if (!file_exists($database_path)) {
    touch($database_path);
    chmod($database_path, 0777);

    if (extension_loaded('pdo_sqlite')) {
        try {
            $pdo = new PDO('sqlite:' . $database_path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Enable foreign keys
            $pdo->exec('PRAGMA foreign_keys = ON');
            
            // Check if migrations table exists
            $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
            if (!$result->fetch()) {
                // Create migrations table
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS migrations (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        migration VARCHAR NOT NULL,
                        batch INTEGER NOT NULL
                    )
                ");
                
                // Run migrations
                chdir(dirname(__DIR__));
                passthru('php artisan migrate --force --no-interaction 2>&1', $return_var);
                if ($return_var !== 0) {
                    error_log("Migration failed with status: " . $return_var);
                }
            } else {
                // Check if tasks table exists
                $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tasks'");
                if (!$result->fetch()) {
                    // Run migrations if tasks table is missing
                    chdir(dirname(__DIR__));
                    passthru('php artisan migrate --force --no-interaction 2>&1', $return_var);
                    if ($return_var !== 0) {
                        error_log("Migration failed with status: " . $return_var);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Database initialization error: " . $e->getMessage());
        }
    }
}

// Create required directories
$dirs = [
    $storage_path . '/framework/cache',
    $storage_path . '/framework/sessions',
    $storage_path . '/framework/views',
    $storage_path . '/logs',
    $storage_path . '/app/public',
    $bootstrap_path . '/cache'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    chmod($dir, 0777);
}

// Set storage directory symlink
$public_storage = __DIR__ . '/storage';
if (!file_exists($public_storage)) {
    @symlink($storage_path, $public_storage);
}

// Continue with normal application bootstrapping
require __DIR__ . '/index.php';